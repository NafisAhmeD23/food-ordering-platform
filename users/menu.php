<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "food_ordering_platform";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch food items
$sql = "SELECT name, description, price, category, image, available FROM food_items";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering Platform</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .food-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 40px;
        }

        .food-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            transition: transform 0.2s;
        }

        .food-card:hover {
            transform: scale(1.05);
        }

        .food-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
        }

        .food-card h3 {
            margin: 10px 0 5px;
        }

        .food-card p {
            font-size: 14px;
            color: #555;
        }

        .food-card .price {
            margin: 10px 0;
            font-weight: bold;
            color: #e63946;
        }

        .food-card .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #e63946;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .food-card .btn:hover {
            background: #c92c3a;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <nav>
            <div class="logo">🍴 Food Ordering</div>
            <ul>
                <li><a href="users/menu.php">Menu</a></li>
                <li><a href="users/cart.php">Cart</a></li>
                <li><a href="users/my_orders.php">Order</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Our Food Ordering Platform</h1>
        <p>Order your favorite meals online, quick and easy.</p>
        <a href="users/menu.php" class="btn">Browse Menu</a>
    </section>

    <!-- Food Items Section -->
    <section>
        <h2 style="text-align:center; margin:30px 0;">Our Menu</h2>
        <div class="food-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Skip unavailable items
                    if (!$row['available'])
                        continue;

                    echo '<div class="food-card">';
                    echo '<img src="../assets/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '<div class="price">$' . number_format($row['price'], 2) . '</div>';
                    echo '<a href="users/cart.php?add=' . urlencode($row['name']) . '" class="btn">Add to Cart</a>';
                    echo '</div>';
                }
            } else {
                echo "<p style='text-align:center;'>No food items available right now.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>© <?php echo date("Y"); ?> Food Ordering Platform. All Rights Reserved.</p>
    </footer>

</body>

</html>
<?php $conn->close(); ?>