<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'food_ordering_platform';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {

        // Use the cart stored in session
        $cart = $_SESSION['checkout_cart'] ?? [];

        if (empty($cart)) {
            echo "<h2>❌ Your cart is empty!</h2>";
            exit;
        }

        $total_price = 0;
        $items_list = [];

        foreach ($cart as $name => $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];
            $line_total = $price * $quantity;
            $total_price += $line_total;

            $items_list[] = $name . " (x" . $quantity . ")";
        }

        $items_text = implode(", ", $items_list);

        // Insert into myorders table
        $stmt = $pdo->prepare("INSERT INTO myorders (items, total_price) VALUES (?, ?)");
        $stmt->execute([$items_text, $total_price]);
        $order_id = $pdo->lastInsertId();

        echo "<h2>✅ Checkout Successfully. Order ID: {$order_id}</h2>";
        echo "<p>Items: {$items_text}</p>";
        echo "<p>Total Price: {$total_price}৳</p>";

        // Clear cart
        unset($_SESSION['cart']);
        unset($_SESSION['checkout_cart']);
    } else {
        echo "<h2>❌ Checkout Cancelled</h2>";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Checkout</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                margin-top: 100px;
            }

            button {
                padding: 10px 20px;
                font-size: 16px;
                margin: 10px;
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <h2>🛒 Do you want to checkout?</h2>
        <form method="post">
            <button type="submit" name="confirm" value="yes">Yes</button>
            <button type="submit" name="confirm" value="no">No</button>
        </form>
    </body>

    </html>
    <?php
}
?>