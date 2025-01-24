<?php

include('header.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Link your CSS file here -->
    <style>
        /* Add your custom CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            flex: 1;
            background-color: #333;
            color: #fff;
            padding: 20px;
        }
        .sidebar h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            transition: background 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: #575757;
        }
        .content {
            flex: 3;
            padding: 20px;
        }
        .content h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="gerar_home.php">Home</a></li>
                <li><a href="admin_courses.php">Courses</a></li>
                <li><a href="admin_users.php">Users</a></li>
                <li><a href="admin_reports.php">Reports</a></li>
                <li><a href="admin_messages.php">Messages</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="content">
            <h2>Welcome, Admin</h2>
            <p>This is your dashboard. You can manage courses, users, view reports, and more.</p>

            <!-- Dynamic content can be added here, like a summary or recent activity -->
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>
