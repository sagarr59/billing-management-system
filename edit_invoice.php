<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    die("Invalid invoice ID");
}

// Fetch invoice details
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die("Invoice not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];
    $issued_date = $_POST['issued_date'];
    $total_amount = floatval($_POST['total_amount']);
    $discount = floatval($_POST['discount']);
    $net_amount = $total_amount - $discount;

    try {
        // Log old data
        $log_stmt = $pdo->prepare("
            INSERT INTO invoice_edit_log (
                invoice_id, invoice_number, reference_no, old_client_name,
                old_issued_date, old_total, old_discount, old_net,
                edited_by, edited_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $log_stmt->execute([
            $invoice['id'],
            $invoice['invoice_number'],
            $invoice['reference_no'],
            $invoice['client_name'],
            $invoice['issued_date'],
            $invoice['total_amount'],
            $invoice['discount'],
            $invoice['net_amount'],
            $_SESSION['admin_id']
        ]);

        // Update invoice
        $update_stmt = $pdo->prepare("
            UPDATE invoices
            SET client_name = ?, issued_date = ?, total_amount = ?, discount = ?, net_amount = ?
            WHERE id = ?
        ");
        $update_stmt->execute([$client_name, $issued_date, $total_amount, $discount, $net_amount, $invoice_id]);

        header("Location: admin_panel.php");
        exit;
    } catch (PDOException $e) {
        echo "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Invoice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .edit-wrapper {
      max-width: 700px;
      margin: 50px auto;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      padding: 2rem;
      background-color: #ffffff;
    }
    h2 {
      color: #343a40;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
    .form-label {
      font-weight: 500;
      color: #495057;
    }
    .btn-primary {
      border-radius: 8px;
      padding: 0.5rem 1.5rem;
    }
    .btn-secondary {
      border-radius: 8px;
      padding: 0.5rem 1.5rem;
    }
  </style>
</head>
<body>

<div class="edit-wrapper">
  <div class="card">
    <h2>Edit Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h2>

    <form method="POST">
      <div class="mb-3">
        <label for="client_name" class="form-label">Client Name</label>
        <input type="text" class="form-control" id="client_name" name="client_name" value="<?= htmlspecialchars($invoice['client_name']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="issued_date" class="form-label">Issued Date</label>
        <input type="date" class="form-control" id="issued_date" name="issued_date" value="<?= htmlspecialchars($invoice['issued_date']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="total_amount" class="form-label">Total Amount (Rs)</label>
        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="<?= htmlspecialchars($invoice['total_amount']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="discount" class="form-label">Discount (Rs)</label>
        <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="<?= htmlspecialchars($invoice['discount']) ?>" required>
      </div>

      <div class="d-flex justify-content-between mt-4">
        <a href="admin_panel.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Invoice</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
