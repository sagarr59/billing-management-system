<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in as admin
    exit();
}

// Include database connection
include('db_connection.php'); // Make sure you have a file for DB connection

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $business_name = $_POST['business_name'];
    $business_address = $_POST['business_address'];
    $invoice_format = $_POST['invoice_format'];

    // Handle file upload (Logo)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo_tmp_name = $_FILES['logo']['tmp_name'];
        $logo_name = $_FILES['logo']['name'];
        $logo_size = $_FILES['logo']['size'];
        $logo_type = $_FILES['logo']['type'];

        // Check if the file is an image
        $allowed_extensions = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($logo_type, $allowed_extensions)) {
            $upload_dir = 'uploads/logos/';
            $logo_path = $upload_dir . basename($logo_name);

            // Create the uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move the uploaded logo to the specified directory
            if (move_uploaded_file($logo_tmp_name, $logo_path)) {
                // Logo uploaded successfully, save the file path
                $logo_path = $logo_path;
            } else {
                $error_message = "Logo upload failed!";
            }
        } else {
            $error_message = "Invalid logo file type!";
        }
    }

    // Prepare the SQL query to update settings
    $sql = "UPDATE settings SET 
            business_name = ?, 
            business_address = ?, 
            invoice_format = ?, 
            logo = ? 
            WHERE id = 1";  // Assuming a single row for settings with ID 1

    // Prepare the statement and bind parameters
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $business_name, $business_address, $invoice_format, $logo_path);
        
        // Execute the statement
        if ($stmt->execute()) {
            $success_message = "Settings updated successfully!";
        } else {
            $error_message = "Error updating settings: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        $error_message = "Failed to prepare the SQL statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Settings | Ambience Infosys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h2>Settings Update Status</h2>
        </div>

        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="settings.php" class="btn btn-primary mt-3">Back to Settings</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
