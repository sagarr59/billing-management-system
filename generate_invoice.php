<?php
// Set the timezone to Nepal Standard Time (Nepal is UTC +5:45)
date_default_timezone_set('Asia/Kathmandu');

// Enable error reporting
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
}

// Function to Create an Invoice
function createInvoice($date, $clientName, $address, $particulars, $discount, $netAmount) {
    global $pdo;
    
    $referenceNo = 'REF-' . strtoupper(substr(uniqid(), -6));  // Generates a unique reference number
    $invoice_number = 'AI-' . time(); // Automatically generate a unique invoice number

    // Insert Invoice into the database
    try {
        $sql = "INSERT INTO invoices (issued_date, reference_no, client_name, address, total_amount, discount, net_amount, invoice_number) 
                VALUES (:issued_date, :reference_no, :client_name, :address, :total_amount, :discount, :net_amount, :invoice_number)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':issued_date' => $date,
            ':reference_no' => $referenceNo,
            ':client_name' => $clientName,
            ':address' => $address,
            ':total_amount' => $discount + $netAmount, // Total Due is no longer needed
            ':discount' => $discount,
            ':net_amount' => $netAmount,
            ':invoice_number' => $invoice_number
        ]);

        $invoice_id = $pdo->lastInsertId();

        // Insert Particulars
        foreach ($particulars as $particular) {
            $sql = "INSERT INTO invoice_particulars (invoice_id, description, amount) 
                    VALUES (:invoice_id, :description, :amount)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':invoice_id' => $invoice_id,
                ':description' => $particular['name'],
                ':amount' => $particular['amount']
            ]);
        }

        return $invoice_id;
    } catch (PDOException $e) {
        echo "Error while inserting invoice: " . $e->getMessage();
        exit;
    }
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
            <!-- Left Section (Ref No, PAN No, Client Name) -->
            <div style='display: inline-block; width: 48%; font-size: 14px; margin-top: 30px;'>
                <p><strong>Ref No:</strong> {$invoice['reference_no']}</p>
                <p><strong>PAN No:</strong> 615290951</p>
                <p><strong>Client Name:</strong> {$invoice['client_name']}</p>
            </div>
            <!-- Right Section (Date, Invoice No) -->
            <div style='display: inline-block; width: 48%; text-align: right; font-size: 14px;' >
                <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p> <!-- Use real-time date -->
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

        // Loop through and display all the particulars
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
            <p><strong>In Words:</strong> " . convertNumberToWords($netAmount) . " only</p>
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
    // Arrays for number-to-word conversion
    $ones = array(
        1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen'
    );
    
    $tens = array(
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 6 => 'Sixty',
        7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    );

    $thousands = array('', 'Thousand', 'Million', 'Billion'); // Can be extended further

    if ($num == 0) {
        return 'Zero';
    }

    // Initialize the result string
    $words = '';

    // Split the number into chunks of 3 digits
    $numChunks = array();
    $i = 0;

    while ($num > 0) {
        $numChunks[$i] = $num % 1000;
        $num = (int)($num / 1000);
        $i++;
    }

    // Process each chunk and convert it to words
    for ($i = 0; $i < count($numChunks); $i++) {
        if ($numChunks[$i] == 0) {
            continue;
        }
        
        $chunkWords = '';

        // Process hundreds
        if ($numChunks[$i] >= 100) {
            $chunkWords .= $ones[(int)($numChunks[$i] / 100)] . ' Hundred ';
            $numChunks[$i] = $numChunks[$i] % 100;
        }

        // Process tens
        if ($numChunks[$i] >= 20) {
            $chunkWords .= $tens[(int)($numChunks[$i] / 10)] . ' ';
            $numChunks[$i] = $numChunks[$i] % 10;
        }

        // Process ones (1-19)
        if ($numChunks[$i] > 0) {
            $chunkWords .= $ones[$numChunks[$i]] . ' ';
        }

        // Append the chunk to the result
        $words = $chunkWords . $thousands[$i] . ' ' . $words;
    }

    // Trim any trailing whitespace
    return trim($words) . ' only';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['date'];
    $clientName = $_POST['clientName'];
    $address = $_POST['address'];

    if (isset($_POST['particularsData']) && !empty($_POST['particularsData'])) {
        $particulars = json_decode($_POST['particularsData'], true);
    } else {
        $particulars = [];
    }

    $discount = $_POST['discount'];
    $netAmount = array_sum(array_column($particulars, 'amount')) - $discount;

    // Create the invoice and generate PDF
    $invoice_id = createInvoice($date, $clientName, $address, $particulars, $discount, $netAmount);
    generatePDF($invoice_id);
    exit;
}
?>
