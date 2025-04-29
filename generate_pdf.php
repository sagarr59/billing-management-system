<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Dompdf for PDF generation

use Dompdf\Dompdf;
use Dompdf\Options;

// Database Connection
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=Invoice;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Check if the file is accessed with an 'id' parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $invoice_id = intval($_GET['id']);
    
    // Generate the PDF and make it downloadable
    generatePDF($invoice_id);
    exit;
} else {
    echo "Invoice ID is missing or invalid.";
    exit;
}

// Function to generate the PDF
function generatePDF($invoice_id) {
    global $pdo;

    // Get invoice details
    try {
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = :id");
        $stmt->execute([':id' => $invoice_id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            echo "Invoice not found!";
            exit;
        }

        // Get the particulars for the invoice
        $stmt = $pdo->prepare("SELECT * FROM invoice_particulars WHERE invoice_id = :invoice_id");
        $stmt->execute([':invoice_id' => $invoice_id]);
        $particulars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare HTML content for PDF
        $html = "
        <div style='text-align: center;'>
            <h2 style='margin: 0; font-size: 24px;'>Ambience Infosys Pvt. Ltd.</h2>
        </div>
        <div style='margin-top: 20px; width: 100%;'>
            <div style='display: inline-block; width: 48%; font-size: 14px; margin-top: 30px;'>
                <p><strong>Ref No:</strong> {$invoice['reference_no']}</p>
                <p><strong>PAN No:</strong> 615290951</p>
                <p><strong>Client Name:</strong> {$invoice['client_name']}</p>
            </div>
            <div style='display: inline-block; width: 48%; text-align: right; font-size: 14px;'>
                <p><strong>Date:</strong> {$invoice['issued_date']}</p>
                <p><strong>Invoice No:</strong> {$invoice['invoice_number']}</p>
            </div>
        </div>
        <div style='margin-top: 30px;'>
            <p><strong>Your Account has been debited as per particulars given below:</strong></p>
            <table style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='border-bottom: 1px solid #000;'>
                        <th style='text-align: left; padding: 8px;'>SN.</th>
                        <th style='text-align: left; padding: 8px;'>PARTICULARS</th>
                        <th style='text-align: right; padding: 8px;'>AMOUNT</th>
                    </tr>
                </thead>
                <tbody>";

        $sn = 1;
        $totalAmount = 0;
        foreach ($particulars as $particular) {
            $html .= "<tr>
                        <td style='padding: 8px;'>{$sn}</td>
                        <td style='padding: 8px;'>{$particular['description']}</td>
                        <td style='padding: 8px; text-align: right;'>Rs. " . number_format($particular['amount'], 2) . "</td>
                      </tr>";
            $totalAmount += $particular['amount'];
            $sn++;
        }

        $html .= "</tbody></table>";

        // Calculate Net Amount (Total Amount - Discount)
        $netAmount = $totalAmount - $invoice['discount'];
        $html .= "
        <div style='margin-top: 20px; text-align: right;'>
            <p><strong>Total Amount:</strong> Rs. " . number_format($totalAmount, 2) . "</p>
            <p><strong>Discount:</strong> Rs. " . number_format($invoice['discount'], 2) . "</p>
            <p><strong>Net Amount:</strong> Rs. " . number_format($netAmount, 2) . "</p>
        </div>

        <div style='margin-top: 30px;'>
            <p><strong>In Words:</strong> " . convertNumberToWords($netAmount) . " </p>
        </div>

        <div style='margin-top: 40px; text-align: right;'>
            <p>Authorized Signature</p>
        </div>";

        // Initialize Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the PDF directly to the browser for download
        $dompdf->stream("invoice_{$invoice['invoice_number']}.pdf", array("Attachment" => 1));

    } catch (Exception $e) {
        echo "Error while generating PDF: " . $e->getMessage();
        exit;
    }
}

// Function to convert number to words (for 'In Words' section)
function convertNumberToWords($num) {
    // Conversion logic 
    $words = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten'
    );
    
    if ($num <= 10) {
        return $words[$num];
    }
    
    return $num;  // Default to just returning the number if it's greater than 10
}
?>
