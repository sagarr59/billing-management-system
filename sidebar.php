
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --sidebar-width: 240px;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    .layout-wrapper {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: var(--sidebar-width);
      background-color: #2c3e50;
      color: white;
      padding: 20px;
    }

    .sidebar .nav-link {
      color: white;
      margin-bottom: 0.5rem;
    }

    
.sidebar .nav-link.active {
  font-weight: bold;
  background-color: #1e2e3e; /* hover-like shade */
  color: #fff;
}


    .sidebar .nav-link:hover {
      background-color: rgba(255,255,255,0.1);
    }

    .main-content {
      flex: 1;
      padding: 2rem;
    }

    .logout {
      color:rgb(255, 0, 0) !important;
      margin-top: auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 767.98px) {
      .sidebar {
        display: none;
      }
    }
  </style>
</head>
<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="layout-wrapper">
  <div class="sidebar d-flex flex-column">
    <h4>Admin Dashboard</h4>
    <nav class="nav flex-column">
      <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
      <a class="nav-link <?php echo $current_page == 'invoice_form.php' ? 'active' : ''; ?>" href="invoice_form.php">Generate Invoice</a>
      <a class="nav-link <?php echo $current_page == 'admin_panel.php' ? 'active' : ''; ?>" href="admin_panel.php">Admin Panel</a>
      <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">Settings</a>
    </nav>
    <a href="logout.php" class="nav-link logout">Logout</a>
  </div>

  <div class="main-content">
