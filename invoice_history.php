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

// Fetch all invoices from the database
$stmt = $pdo->prepare("SELECT * FROM invoices ORDER BY issued_date DESC");
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your custom CSS for the invoice history page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding-top: 20px;
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 8px 15px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .download-btn {
            background-color: #2196F3;
        }
        .download-btn:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Add Back to Dashboard Button -->
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="dashboard.php" class="btn" style="background-color: #4CAF50; text-decoration: none;">Back to Dashboard</a>
        </div>

        <div class="table-container">
            <h2>Invoice History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Client Name</th>
                        <th>Issued Date</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Net Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['client_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($invoice['issued_date'])); ?></td>
                            <td>Rs. <?php echo number_format($invoice['total_amount'], 2); ?></td>
                            <td>Rs. <?php echo number_format($invoice['discount'], 2); ?></td>
                            <td>Rs. <?php echo number_format($invoice['net_amount'], 2); ?></td>
                            <td>
                                <a href="generate_pdf.php?id=<?php echo $invoice['id']; ?>" class="btn download-btn">Download PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
