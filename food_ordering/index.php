<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering Platform</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- Header -->
    <header>
        <nav>
            <div class="logo">🍴 Food Ordering</div>
            <ul>
                <li><a href="users/menu.php">Menu</a></li>
                <li><a href="users/cart.php">Cart</a></li>
                <li><a href="users/my_orders.php">My Orders</a></li>
                <li><a href="users/login.php">Login</a></li>
                <li><a href="users/register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Our Food Ordering Platform</h1>
        <p>Order your favorite meals online, quick and easy.</p>
        <a href="users/menu.php" class="btn">Browse Menu</a>
    </section>

    <!-- Footer -->
    <footer>
        <p>© <?php echo date("Y"); ?> Food Ordering Platform. All Rights Reserved.</p>
    </footer>

</body>
</html>
