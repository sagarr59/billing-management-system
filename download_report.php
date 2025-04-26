<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'db_connect.php';

// Handle Search Parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Modify SQL to fetch records based on the search term or all records if search is empty
if ($search !== '') {
    $sql = "SELECT * FROM invoices WHERE client_name LIKE :search OR invoice_number LIKE :search";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
} else {
    $sql = "SELECT * FROM invoices"; // Fetch all records when search is empty
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure invoices are found
if (empty($invoices)) {
    echo "No invoices found for the given search.";
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="invoice_summary.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Write the header row to CSV
fputcsv($output, ['Invoice Number', 'Reference No', 'Client Name', 'Issued Date', 'Total Amount', 'Discount', 'Net Amount']);

// Write the invoice data to CSV
foreach ($invoices as $invoice) {
    fputcsv($output, [
        $invoice['invoice_number'],
        $invoice['reference_no'],  // Include reference_no
        $invoice['client_name'],
        $invoice['issued_date'],   // Use issued_date instead of date
        number_format($invoice['total_amount'], 2),
        number_format($invoice['discount'], 2),
        number_format($invoice['net_amount'], 2)
    ]);
}

// Close the output stream
fclose($output);
