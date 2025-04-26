<?php 
session_start(); 
include('sidebar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Ambience Infosys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard-header {
            background-color: #0069d9;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .dashboard-header h2 {
            font-size: 28px;
            font-weight: bold;
        }

        .dashboard-header p {
            font-size: 16px;
        }

        .card {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 20px;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .btn-custom {
            background-color: #0069d9;
            color: white;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .form-control {
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .main-content {
            margin-left: 220px;
            padding: 20px;
        }

        /* Responsive Design */
        @media (max-width: 767px) {
            .dashboard-header h2 {
                font-size: 24px;
            }

            .dashboard-header p {
                font-size: 14px;
            }

            .card-body {
                padding: 15px;
            }

            .card-title {
                font-size: 16px;
            }

            .btn-custom {
                font-size: 14px;
                padding: 10px 18px;
            }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container mt-4">
        
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h2>Manage Settings</h2>
            <p>Update invoice formats, business info, and logo</p>
        </div>

        <!-- Settings Form -->
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card p-4">
                    <div class="card-body">
                        <h5 class="card-title">Update Invoice Settings</h5>
                        <form action="update_settings.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- Business Information -->
                            <div class="form-group">
                                <label for="business_name">Business Name</label>
                                <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Enter business name" required>
                            </div>

                            <div class="form-group">
                                <label for="business_address">Business Address</label>
                                <textarea class="form-control" id="business_address" name="business_address" rows="3" placeholder="Enter business address" required></textarea>
                            </div>

                            <!-- Logo Upload -->
                            <div class="form-group">
                                <label for="logo">Business Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            </div>

                            <!-- Invoice Format -->
                            <div class="form-group">
                                <label for="invoice_format">Invoice Format</label>
                                <select class="form-control" id="invoice_format" name="invoice_format">
                                    <option value="pdf">PDF</option>
                                    <option value="html">HTML</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-custom mt-3">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- /container -->
</div> <!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
