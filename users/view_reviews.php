<?php
session_start();
include("../includes/db.php");


$sql = "SELECT r.*, u.username 
        FROM reviews r 
        JOIN users u ON r.user_id = u.user_id 
        ORDER BY r.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Website Reviews</title>
    <!-- Global CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/style.css">
    <!-- Review Specific CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/review.css">
</head>

<body>
    <h2>User Reviews about our Website</h2>
    <a href="review.php">Write a Review</a>
    <hr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='review-box'>";

            // Username + Date
            echo "<p><strong>" . htmlspecialchars($row['username']) . "</strong>";
            echo "<em>" . date("M d, Y h:i A", strtotime($row['created_at'])) . "</em></p>";

            // Rating stars
            echo "<p>";
            for ($i = 1; $i <= 5; $i++) {
                echo ($i <= $row['rating']) ? "⭐" : "☆";
            }
            echo "</p>";

            // Comment
            echo "<p>" . htmlspecialchars($row['comment']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No reviews yet. Be the first one!</p>";
    }
    ?>
</body>

</html>