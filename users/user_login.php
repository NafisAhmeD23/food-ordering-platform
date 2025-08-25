<?php
session_start();

$error_message = "";


require_once '../includes/db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    
    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        
        if ($password === $user['password']) {
            
            if ($user['role'] == 'user') {
        
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['loggedin'] = true;
                
                
                header("Location: menu.php");
                exit();
            } else {
                $error_message = "Access denied. This is for users only.";
            }
        } else {
            $error_message = "Invalid username or password. Please try again.";
        }
    } else {
        $error_message = "Invalid username or password. Please try again.";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Food Ordering Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: url('../assets/backgroung.jpg') no-repeat center center/cover;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        
        .header {
            background: #4a6cf7;
            padding: 30px 20px;
            color: white;
            text-align: center;
        }
        
        .header h1 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .login-form {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: #4a6cf7;
            outline: none;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: #4a6cf7;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #3a5cd8;
        }
        
        .error-message {
            background: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            <?php if (!empty($error_message)) echo 'display: block;'; else echo 'display: none;'; ?>
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #4a6cf7;
            text-decoration: none;
            font-size: 14px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .footer {
            padding: 20px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Login</h1>
            <p>Welcome back to Food Ordering Platform</p>
        </div>
        
        <div class="login-form">
            <div class="error-message" id="errorMessage">
                <?php echo $error_message; ?>
            </div>
            
            <form id="loginForm" method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username" value="<?php if(isset($_POST['username'])) echo htmlspecialchars($_POST['username']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn" name="login">Login</button>
            </form>
            
            
        </div>
        
        
    </div>

    <script>
        
        document.getElementById('username').addEventListener('input', function() {
            document.getElementById('errorMessage').style.display = 'none';
        });
        
        document.getElementById('password').addEventListener('input', function() {
            document.getElementById('errorMessage').style.display = 'none';
        });
        
        
        <?php if (!empty($error_message)): ?>
        document.getElementById('errorMessage').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>