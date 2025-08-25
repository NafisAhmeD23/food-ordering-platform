<?php
session_start();

// --- Logout Logic ---
// Check if the 'action' parameter is set to 'logout' in the URL.
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all session variables.
    $_SESSION = array();

    // Destroy the session.
    session_destroy();

    // Redirect to the index page.
    header("Location: ../index.php");
    
    // Ensure no more code is executed after the redirect.
    exit();
}
// --- End of Logout Logic ---

// --- User Authentication ---
// Ensure the user is logged in and is an admin.
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include your database connection file.
require_once '../includes/db.php';

// --- Fetch Total Users ---
$user_query = "SELECT COUNT(user_id) AS total_users FROM users";
$user_result = $conn->query($user_query);
$total_users = 0;
if ($user_result) {
    $total_users = $user_result->fetch_assoc()['total_users'];
}

// --- Fetch Total Orders ---
$order_query = "SELECT COUNT(order_id) AS total_orders FROM orders";
$order_result = $conn->query($order_query);
$total_orders = 0;
if ($order_result) {
    $total_orders = $order_result->fetch_assoc()['total_orders'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Ordering Platform</title>
    <style>
        /* CSS styles remain the same... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
        }

        .header {
            background: #4a6cf7;
            padding: 30px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-weight: 600;
            font-size: 28px;
            margin: 0;
        }

        .header .user-info {
            text-align: right;
        }

        .header .user-info span {
            display: block;
            opacity: 0.9;
        }

        /* Styling for the new Menu button */
        .header .menu-btn {
            background: #fff;
            color: #4a6cf7;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s, color 0.3s;
            margin-top: 5px;
            display: inline-block;
            margin-right: 10px;
            /* Space between buttons */
        }

        .header .menu-btn:hover {
            background: #f0f0f0;
        }

        .header .logout-btn {
            background: #fff;
            color: #4a6cf7;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s, color 0.3s;
            margin-top: 5px;
            display: inline-block;
        }

        .header .logout-btn:hover {
            background: #f0f0f0;
        }

        .dashboard-content {
            padding: 40px 30px;
        }

        .stats-container {
            display: flex;
            gap: 30px;
            justify-content: center;
        }

        .stat-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            flex: 1;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-card h2 {
            color: #495057;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .stat-card .number {
            color: #4a6cf7;
            font-size: 48px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>

                <a href="menu.php" class="menu-btn">Add dish item</a>

                <a href="dashboard.php?action=logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="stats-container">
                <div class="stat-card">
                    <h2>Total Users</h2>
                    <p class="number"><?php echo $total_users; ?></p>
                </div>

                <div class="stat-card">
                    <h2>Total Orders</h2>
                    <p class="number"><?php echo $total_orders; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>