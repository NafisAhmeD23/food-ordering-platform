<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "food_ordering_platform";

// DB connection
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $available = isset($_POST['available']) ? 1 : 0;

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir))
            mkdir($targetDir);
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $image);
    }

    $sql = "INSERT INTO food_items(name, description, price, category, image, available) 
            VALUES ('$name','$description','$price','$category','$image','$available')";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "✅ Item saved successfully!";
    } else {
        $successMessage = "❌ Error: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering Platform</title>
    <link rel="stylesheet" href="admin_menu.css">
    <script>
        // Confirmation before submitting the form
        function confirmSubmit(event) {
            const confirmed = confirm("Are you sure you want to save this food item?");
            if (!confirmed) {
                event.preventDefault();
            }
        }
    </script>
</head>

<body>

    <!-- Header -->
    <header>
        <nav>
            <div class="logo">🍴 Food Ordering</div>
            <ul>
                <li><a href="users/menu.php">Menu</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2 class="mb-4">Add Item</h2>

        <!-- Success / Error Message -->
        <?php if (!empty($successMessage)): ?>
            <?= $successMessage ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="confirmSubmit(event)">
        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label for="price" class="form-label">Price ($)</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>

        <!-- Category -->
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                <option value="">Select a category</option>
                <option value="Pizza">Pizza</option>
                <option value="Drink">Drink</option>
                <option value="Desert">Desert</option>
            </select>
        </div>

        <!-- Image -->
        <div class="mb-3">
            <label for="image" class="form-label">Upload Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <!-- Availability -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="available" name="available" value="1">
            <label class="form-check-label" for="available">
                Available
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Save Item</button>
    </form>
    </div>

</body>

</html>