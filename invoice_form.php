<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Generate Invoice | Ambience Infosys</title>
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
  height: 100%;
  position: fixed; /* Fix the sidebar to the left */
  top: 0;
  left: 0;
}

.sidebar .nav-link {
  color: white;
  margin-bottom: 0.5rem;
}

.sidebar .nav-link.active {
  font-weight: bold;
  background-color: #1e2e3e;
  color: #fff;
}

.sidebar .nav-link:hover {
  background-color: rgba(255,255,255,0.1);
}

.main-content {
  margin-left: var(--sidebar-width); /* Ensure the content doesn't overlap with the sidebar */
  flex: 1;
  padding: 2rem;
  background-color: #f8f9fa;
  height: 100%;
}

.logout {
  color:rgb(255, 0, 0) !important;
  margin-top: auto;
  display: flex;
  align-items: left;
  justify-content: center;
}

@media (max-width: 767.98px) {
  .sidebar {
    display: none;
  }
  .main-content {
    margin-left: 0;
  }
}

body {
  padding: 0; /* Remove padding on body */
}

.brand-header {
  background-color: #2c3e50;
  color: white;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 30px;
}

.brand-header h2 {
  margin: 0;
}

  </style>
</head>
<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="layout-wrapper">
  <!-- Sidebar -->
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

  <!-- Main Content -->
  <div class="main-content">
    <div class="brand-header">
        <h2>Ambience Infosys Pvt. Ltd.</h2>
        <p>Delivering Innovative IT Solutions & Training</p>
    </div>

    <div class="container bg-white p-4 rounded shadow-sm">
        <h4 class="mb-4">Invoice Generation Form</h4>

        <form action="generate_invoice.php" method="POST" id="invoiceForm">
        <a href="admin_panel.php" class="btn btn-outline-primary mb-3">&larr; Back to Admin Dashboard</a>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Client Name</label>
                    <input type="text" name="clientName" class="form-control" placeholder="e.g. ***** Enterprises" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Client Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Client location..." required></textarea>
            </div>

            <h5 class="mt-4">Particulars</h5>
            <div id="particulars">
                <div class="row mb-2">
                    <div class="col-md-6"><input type="text" class="form-control" placeholder="Service/Product Name" required></div>
                    <div class="col-md-4"><input type="number" class="form-control" placeholder="Amount (Rs.)" required></div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger w-100" onclick="removeItem(this)">Remove</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="addParticular()">+ Add Item</button>

            <div class="mb-3">
                <label>Discount (Rs.)</label>
                <input type="number" name="discount" class="form-control" value="0" required>
            </div>

            <input type="hidden" name="particularsData" id="particularsData">

            <button type="submit" class="btn btn-primary">Generate Invoice PDF</button>
        </form>
    </div>
  </div>
</div>

<script>
    function addParticular() {
        const row = `
            <div class="row mb-2">
                <div class="col-md-6"><input type="text" class="form-control" placeholder="Service/Product Name" required></div>
                <div class="col-md-4"><input type="number" class="form-control" placeholder="Amount (Rs.)" required></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100" onclick="removeItem(this)">Remove</button>
                </div>
            </div>`;
        document.getElementById('particulars').insertAdjacentHTML('beforeend', row);
    }

    function removeItem(button) {
        button.closest('.row').remove();
    }

    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#particulars .row');
        const data = [];

        rows.forEach(row => {
            const name = row.querySelector('input[type="text"]').value;
            const amount = parseFloat(row.querySelector('input[type="number"]').value);
            if (name && !isNaN(amount)) {
                data.push({ name, amount });
            }
        });

        document.getElementById('particularsData').value = JSON.stringify(data);
    });
</script>

</body>
</html>
