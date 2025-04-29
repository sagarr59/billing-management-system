<?php 
session_start(); 

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}

include('sidebar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Ambience Infosys</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
    }

    
    .main-content {
      padding: 30px 20px;
      transition: all 0.3s ease;
    }

    @media (min-width: 768px) {
      .main-content {
        margin-left: 20px; 
      }
    }

    .dashboard-header {
      background: #fff;
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
      margin-bottom: 30px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 15px;
    }

    .dashboard-header .logo {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .dashboard-header .logo img {
      height: 50px;
      border-radius: 8px;
    }

    .card {
      border: none;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(6px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-4px);
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #333;
    }

    .btn-custom {
      background-color: #0069d9;
      color: white;
      border-radius: 8px;
    }

    .btn-custom:hover {
      background-color: #0056b3;
    }

    .list-group-item {
      border: none;
      padding: 0.75rem 0;
      font-weight: 500;
      background-color: transparent;
    }

    .list-group-item a {
      color: #444;
      text-decoration: none;
      transition: 0.2s ease;
    }

    .list-group-item a:hover {
      color: #0069d9;
    }

    @media (max-width: 767px) {
      .main-content {
        margin-left: 0;
        padding: 20px 15px;
      }

      .dashboard-header {
        flex-direction: column;
        text-align: center;
        padding: 20px;
      }

      .dashboard-header .logo {
        justify-content: center;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="container-fluid">

    <!-- Header -->
    <div class="dashboard-header">
      <div class="logo">
        <img src="ambienceLogo.jpg" alt="Ambience Infosys Logo">
        <div>
          <h2 class="mb-0">Ambience Infosys</h2>
          <small class="text-muted">Admin Portal</small>
        </div>
      </div>
      <p class="mb-0 fw-semibold text-secondary"><i class="fas fa-chart-line me-2"></i>Efficient Invoice Management & Operations</p>
    </div>

    <!-- Top Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="card p-4">
          <div class="card-body">
            <h5 class="card-title">Welcome Back, Admin!</h5>
            <p class="text-muted">Manage invoices, view reports, and perform actions quickly.</p>
            <a href="invoice_form.php" class="btn btn-custom"><i class="fas fa-file-invoice"></i> Generate Invoice</a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card p-4">
          <div class="card-body">
            <h5 class="card-title">Quick Access</h5>
            <ul class="list-group list-group-flush mt-3">
              <li class="list-group-item"><a href="invoice_history.php"><i class="fas fa-history me-2"></i>View Invoice History</a></li>
              <li class="list-group-item"><a href="invoice_form.php"><i class="fas fa-plus me-2"></i>Create New Invoice</a></li>
              <li class="list-group-item"><a href="download_report.php"><i class="fas fa-download me-2"></i>Download Reports</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Settings Card -->
    <div class="row">
      <div class="col-md-6">
        <div class="card p-4 text-center">
          <div class="card-body">
            <h5 class="card-title">System Settings</h5>
            <p class="text-muted">Customize invoice templates, update logo & business info.</p>
            <a href="settings.php" class="btn btn-outline-secondary"><i class="fas fa-cog me-2"></i>Manage Settings</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
