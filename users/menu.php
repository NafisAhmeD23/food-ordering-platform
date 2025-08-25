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

// --- CART SYSTEM --- //
// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if (isset($_GET['add'])) {
    $foodName = $conn->real_escape_string($_GET['add']);
    $sql = "SELECT name, price FROM food_items WHERE name='$foodName' AND available=1 LIMIT 1";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $item = $res->fetch_assoc();
        $name = $item['name'];
        $price = $item['price'];

        // If item already in cart, increase qty
        if (isset($_SESSION['cart'][$name])) {
            $_SESSION['cart'][$name]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$name] = [
                "price" => $price,
                "quantity" => 1
            ];
        }
    }
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $removeName = $_GET['remove'];
    unset($_SESSION['cart'][$removeName]);
}

// Checkout: save order in DB
if (isset($_POST['checkout'])) {
    if (!empty($_SESSION['cart'])) {
        $orderItems = [];
        $totalPrice = 0;

        foreach ($_SESSION['cart'] as $name => $item) {
            $orderItems[] = $name . " x" . $item['quantity'];
            $totalPrice += $item['price'] * $item['quantity'];
        }

        $itemsString = implode(", ", $orderItems);

        $stmt = $conn->prepare("INSERT INTO myorders (items, total_price) VALUES (?, ?)");
        $stmt->bind_param("sd", $itemsString, $totalPrice);
        $stmt->execute();
        $stmt->close();

        // Clear cart after checkout
        $_SESSION['cart'] = [];
        $successMessage = "Order placed successfully!";
    }
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

        .cart {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .cart table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart th,
        .cart td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .cart th {
            background: #f4f4f4;
        }

        .success {
            text-align: center;
            color: green;
            font-weight: bold;
            margin: 20px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <nav>
            <div class="logo">🍴 Food Ordering</div>
            <ul>
                <li><a href="#cart">Cart</a></li>
                <li><a href="review.php">Give Review</a></li>
                <li><a href="index.php">logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Our Food Ordering Platform</h1>
        <p>Order your favorite meals online, quick and easy.</p>
        <a href="#menu" class="btn">Browse Menu</a>
    </section>

    <!-- Food Items Section -->
    <section id="menu">
        <h2 style="text-align:center; margin:30px 0;">Our Menu</h2>
        <div class="food-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (!$row['available'])
                        continue;

                    echo '<div class="food-card">';
                    echo '<img src="../assets/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '<div class="price">$' . number_format($row['price'], 2) . '</div>';
                    echo '<a href="?add=' . urlencode($row['name']) . '" class="btn">Add to Cart</a>';
                    echo '</div>';
                }
            } else {
                echo "<p style='text-align:center;'>No food items available right now.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Cart Section -->
    <section id="cart" class="cart">
        <h2>🛒 Your Cart</h2>
        <?php
        if (isset($successMessage)) {
            echo "<p class='success'>$successMessage</p>";
        }

        if (!empty($_SESSION['cart'])) {
            echo "<form method='POST'>";
            echo "<table>";
            echo "<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Subtotal</th><th>Action</th></tr>";

            $total = 0;
            foreach ($_SESSION['cart'] as $name => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                echo "<tr>";
                echo "<td>" . htmlspecialchars($name) . "</td>";
                echo "<td>" . $item['quantity'] . "</td>";
                echo "<td>$" . number_format($item['price'], 2) . "</td>";
                echo "<td>$" . number_format($subtotal, 2) . "</td>";
                echo "<td><a href='?remove=" . urlencode($name) . "' style='color:red;'>Remove</a></td>";
                echo "</tr>";
            }

            echo "<tr><th colspan='3'>Total</th><th colspan='2'>$" . number_format($total, 2) . "</th></tr>";
            echo "</table>";
            echo "<br><button type='submit' name='checkout' class='btn'>Checkout</button>";
            echo "</form>";
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
    </section>

    <!-- Footer -->
    <footer>
        <p>© <?php echo date("Y"); ?> Food Ordering Platform. All Rights Reserved.</p>
    </footer>

</body>

</html>
<?php $conn->close(); ?>