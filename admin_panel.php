<?php
session_start();
include('sidebar.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

include 'db_connect.php';

// Handle delete with remarks
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_invoice'])) {
    $invoice_id = intval($_POST['invoice_id']);
    $remarks = $_POST['remarks'];

    try {
        $fetch_stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
        $fetch_stmt->execute([$invoice_id]);
        $invoice = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

        if ($invoice) {
            $log_stmt = $pdo->prepare("INSERT INTO invoice_deletion_log (
                invoice_id, invoice_number, reference_no, client_name, issued_date,
                total_amount, discount, net_amount, remarks, deleted_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $log_stmt->execute([
                $invoice['id'],
                $invoice['invoice_number'],
                $invoice['reference_no'],
                $invoice['client_name'],
                $invoice['issued_date'],
                $invoice['total_amount'],
                $invoice['discount'],
                $invoice['net_amount'],
                $remarks
            ]);

            $delete_stmt = $pdo->prepare("DELETE FROM invoices WHERE id = ?");
            $delete_stmt->execute([$invoice_id]);

            $_SESSION['message'] = "Invoice deleted and logged successfully.";
        }

        header("Location: admin_panel.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Search logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $sql = "
      SELECT * FROM invoices
      WHERE client_name LIKE :searchName
         OR invoice_number LIKE :searchNumber
      ORDER BY issued_date DESC
    ";
    $stmt = $pdo->prepare($sql);
    $like = "%{$search}%";
    $stmt->bindValue(':searchName', $like, PDO::PARAM_STR);
    $stmt->bindValue(':searchNumber', $like, PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM invoices ORDER BY issued_date DESC";
    $stmt = $pdo->prepare($sql);
}
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- Summary Card Queries ----------

// Total invoices
$totalInvoices = $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();

// Total Net Amount
$totalNetAmount = $pdo->query("SELECT SUM(net_amount) FROM invoices")->fetchColumn();
$totalNetAmount = $totalNetAmount ?: 0;

// Monthly Revenue
$currentMonth = date('Y-m');
$monthlyStmt = $pdo->prepare("SELECT SUM(net_amount) FROM invoices WHERE DATE_FORMAT(issued_date, '%Y-%m') = ?");
$monthlyStmt->execute([$currentMonth]);
$monthlyRevenue = $monthlyStmt->fetchColumn();
$monthlyRevenue = $monthlyRevenue ?: 0;


$monthlyLabels = [];
$monthlyData = [];

$dt = new DateTime('first day of this month');

for ($i = 5; $i >= 0; $i--) {
    $monthDt = clone $dt;
    $monthDt->modify("-{$i} months");
    $month = $monthDt->format('Y-m'); // For DB query
    $label = $monthDt->format('M');   // Correct label like Jan, Feb, etc.

    $stmt = $pdo->prepare("SELECT SUM(net_amount) as revenue FROM invoices WHERE DATE_FORMAT(issued_date, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $revenue = $stmt->fetchColumn();

    $monthlyLabels[] = $label;
    $monthlyData[] = $revenue ?: 0;
}


// Top Client
$topClientStmt = $pdo->query("SELECT client_name, COUNT(*) as invoice_count FROM invoices GROUP BY client_name ORDER BY invoice_count DESC LIMIT 1");
$topClient = $topClientStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <title>Admin Panel | Ambience Infosys</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .main-content {
      flex: 1;
      padding: 2rem;
      background-color: #f8f9fa;
      min-height: 100vh;
    }
    .content-header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      gap: .5rem;
    }
    .search-bar .form-control {
      border-radius: 50px 0 0 50px;
    }
    .search-bar .btn {
      border-radius: 0 50px 50px 0;
    }
    .table-responsive {
      background: #fff;
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .table thead {
      background-color: #0069d9;
      color: #fff;
    }
    .table-hover tbody tr:hover {
      background-color: #e9f2ff;
    }
    .btn-sm {
      border-radius: 4px;
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="container-fluid">

    <!-- Header -->
    <div class="content-header">
      <h2>Invoice Summary</h2>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <form class="search-bar d-flex" method="GET" action="admin_panel.php">
          <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   class="form-control" placeholder="Search client or invoice…">
            <button class="btn btn-primary" type="submit">🔍</button>
          </div>
        </form>
        <a href="admin_panel.php" class="btn btn-outline-secondary">Show All</a>
        <a href="download_report.php?search=<?= urlencode($search) ?>" class="btn btn-success">Download Report</a>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Invoices</h5>
            <p class="card-text fs-4"><?= $totalInvoices ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Net Amount</h5>
            <p class="card-text fs-4">Rs. <?= number_format($totalNetAmount, 2) ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Monthly Revenue</h5>
            <p class="card-text fs-4">Rs. <?= number_format($monthlyRevenue, 2) ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Top Client</h5>
            <p class="card-text fs-6"><?= $topClient ? htmlspecialchars($topClient['client_name']) : 'N/A' ?></p>
            <small><?= $topClient ? $topClient['invoice_count'] . ' invoices' : '' ?></small>
          </div>
        </div>
      </div>
    </div>



    <!-- Revenue Chart -->
<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <h5 class="card-title">Monthly Revenue Overview</h5>
    <canvas id="revenueChart" height="100"></canvas>
  </div>
</div>


    <!-- Invoices Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Invoice No</th>
            <th>Ref No</th>
            <th>Client</th>
            <th>Date Issued</th>
            <th class="text-end">Total</th>
            <th class="text-end">Discount</th>
            <th class="text-end">Net Amt</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if($invoices): ?>
          <?php foreach($invoices as $inv): ?>
            <tr>
              <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
              <td><?= htmlspecialchars($inv['reference_no']) ?></td>
              <td><?= htmlspecialchars($inv['client_name']) ?></td>
              <td><?= date('Y-m-d', strtotime($inv['issued_date'])) ?></td>

              <td class="text-end">Rs. <?= number_format($inv['total_amount'],2) ?></td>
              <td class="text-end">Rs. <?= number_format($inv['discount'],2) ?></td>
              <td class="text-end">Rs. <?= number_format($inv['net_amount'],2) ?></td>
              <td class="text-center">
               <a href="generate_invoice.php?id=<?= $inv['id'] ?>" class="btn btn-info btn-sm" target="_blank">View</a>
               <a href="edit_invoice.php?id=<?= $inv['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
               <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $inv['id'] ?>)">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center py-4">No records found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="invoice_id" id="deleteInvoiceId">
        <div class="mb-3">
          <label for="remarks" class="form-label">Reason for deletion</label>
          <textarea id="remarks" name="remarks" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="delete_invoice" class="btn btn-danger">Delete Invoice</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function openDeleteModal(id) {
    document.getElementById('deleteInvoiceId').value = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
  }
</script>

<script>
  const monthlyLabels = <?= json_encode($monthlyLabels) ?>;
  const monthlyData = <?= json_encode($monthlyData) ?>;
</script>

<script>
  const ctx = document.getElementById('revenueChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: monthlyLabels,
      datasets: [{
        label: 'Revenue (Rs)',
        data: monthlyData,
        backgroundColor: '#0d6efd',
        borderRadius: 10,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Rs. ' + Number(context.parsed.y).toLocaleString();
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rs. ' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
</script>


</body>
</html>
