<?php
session_start();


$error_message = "";
$success_message = "";


require_once '../includes/db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssss", $username, $email, $password, $full_name, $address, $phone);
            
            if ($insert_stmt->execute()) {
                $success_message = "Registration successful! You can now login.";
                
                $username = $email = $full_name = $address = $phone = "";
            } else {
                $error_message = "Registration failed. Please try again.";
            }
            
            $insert_stmt->close();
        }
        
        $check_stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - Food Ordering Platform</title>
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
            max-width: 500px;
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
        
        .registration-form {
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
        
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            <?php if (!empty($success_message)) echo 'display: block;'; else echo 'display: none;'; ?>
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
        
        .required::after {
            content: " *";
            color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Registration</h1>
            <p>Create your account for Food Ordering Platform</p>
        </div>
        
        <div class="registration-form">
            <div class="error-message" id="errorMessage">
                <?php echo $error_message; ?>
            </div>
            
            <div class="success-message" id="successMessage">
                <?php echo $success_message; ?>
            </div>
            
            <form id="registrationForm" method="POST" action="">
                <div class="form-group">
                    <label for="username" class="required">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Choose a username" value="<?php if(isset($_POST['username'])) echo htmlspecialchars($_POST['username']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php if(isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="required">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password (min. 6 characters)">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="required">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" value="<?php if(isset($_POST['full_name'])) echo htmlspecialchars($_POST['full_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php if(isset($_POST['address'])) echo htmlspecialchars($_POST['address']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php if(isset($_POST['phone'])) echo htmlspecialchars($_POST['phone']); ?>">
                </div>
                
                <button type="submit" class="btn" name="register">Register</button>
            </form>
            
            <div class="links">
                Already have an account? <a href="user_login.php">Login here</a>
            </div>
        </div>
        
    </div>

    <script>
        
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                document.getElementById('errorMessage').style.display = 'none';
                document.getElementById('successMessage').style.display = 'none';
            });
        });
        
        
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                document.getElementById('errorMessage').textContent = 'Passwords do not match.';
                document.getElementById('errorMessage').style.display = 'block';
            }
        });
        
        
        <?php if (!empty($error_message)): ?>
        document.getElementById('errorMessage').style.display = 'block';
        <?php endif; ?>
        
        
        <?php if (!empty($success_message)): ?>
        document.getElementById('successMessage').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>