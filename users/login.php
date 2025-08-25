<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Selection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('../assets/backgroung.jpg') no-repeat center center/cover;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .login-option {
            display: block;
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .login-option:hover {
            background-color: #0056b3;
        }

        .admin-option {
            background-color: #dc3545;
        }

        .admin-option:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Select Login Type</h2>

        <a href="user_login.php" class="login-option">
            User Login
        </a>

        <a href="../admin/login.php" class="login-option admin-option">
            Admin Login
        </a>
    </div>
</body>

</html>