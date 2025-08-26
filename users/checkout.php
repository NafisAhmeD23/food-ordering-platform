<?php
session_start(); // Assuming cart data is stored in session

// Database connection
$host = 'localhost';
$db = 'food_ordering_platform';
$user = 'root';
$pass = ''; // Update with your actual password
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
        // Simulate user_id and cart for demo purposes
        $user_id = 1; // Replace with actual logged-in user ID
        $cart = $_SESSION['cart'] ?? [
            ['food_id' => 2, 'quantity' => 1],
            ['food_id' => 5, 'quantity' => 2]
        ];

        // Calculate total price and prepare item summary
        $total_price = 0;
        $items_list = [];

        foreach ($cart as $item) {
            $stmt = $pdo->prepare("SELECT name, price FROM food_items WHERE food_id = ?");
            $stmt->execute([$item['food_id']]);
            $food = $stmt->fetch();

            if ($food) {
                $line_total = $food['price'] * $item['quantity'];
                $total_price += $line_total;
                $items_list[] = $food['name'] . " (x" . $item['quantity'] . ")";
            }
        }

        // Convert items array to text for storage in myorders
        $items_text = implode(", ", $items_list);

        // Insert into myorders table
        $stmt = $pdo->prepare("INSERT INTO myorders (items, total_price) VALUES (?, ?)");
        $stmt->execute([$items_text, $total_price]);
        $order_id = $pdo->lastInsertId();

        echo "<h2>✅ Checkout Successfully. Order ID: {$order_id}</h2>";
        echo "<p>Items: {$items_text}</p>";
        echo "<p>Total Price: {$total_price}৳</p>";

        unset($_SESSION['cart']); // Clear cart after checkout
    } else {
        echo "<h2>❌ Checkout Cancelled</h2>";
    }
} else {
    // Show confirmation form
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