<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql  = "SELECT * FROM admins WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login | Ambience Infosys</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-card {
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
      overflow: hidden;
    }
    .login-header {
      background: #fff;              /* white background */
      color: #343a40;                /* dark text */
      padding: 1.5rem 1rem;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }
    .login-header img.logo-img {
      height: 60px;
      margin-bottom: 0.75rem;
    }
    .login-header h3 {
      margin: 0;
      font-weight: 500;
      color: #343a40;
    }
    .login-header small {
      display: block;
      color: #6c757d;
      margin-top: 0.25rem;
    }
    .login-body {
      padding: 2rem;
    }
    .position-relative .input-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
    .form-control {
      border-radius: 0.5rem;
      padding-left: 2.5rem;
    }
    .btn-login {
      width: 100%;
      border-radius: 0.5rem;
      padding: 0.75rem;
    }
    .alert {
      border-radius: 0.5rem;
    }
    .text-center small.footer-text {
      color: #6c757d;
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="login-header">
    <img src="ambienceLogo.jpg" alt="Ambience Infosys Logo" class="logo-img">
    <h3>Ambience Infosys</h3>
    <small>Admin Portal</small>
  </div>
  <div class="login-body">
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
      <div class="mb-3 position-relative">
        <i class="bi bi-person-fill input-icon"></i>
        <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
      </div>
      <div class="mb-3 position-relative">
        <i class="bi bi-lock-fill input-icon"></i>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-login">Login</button>
    </form>
    <div class="text-center mt-3">
      <small class="footer-text">&copy; <?= date('Y') ?> Ambience Infosys Pvt. Ltd.</small>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
