<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    echo "Please login first to submit a review.";
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $comment = trim($_POST['comment']);
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $user_id = $_SESSION['user_id'];


    if (!empty($comment) && $rating >= 1 && $rating <= 5) {
        $sql = "INSERT INTO reviews (user_id, rating, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $rating, $comment);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>✅ Review submitted successfully!</p>";
        } else {
            $message = "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>⚠ Please select a rating (1–5 stars) and write a comment.</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Website Review</title>
    <!-- Global CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/style.css">
    <!-- Review Specific CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/review.css">
</head>

<body>
    <h2>Write a Review about our Website</h2>

    <!-- Show Message -->
    <?php if (!empty($message))
        echo $message; ?>

    <!-- Review Form -->
    <form method="POST">
        <!-- Rating Stars -->
        <div class="rating">
            <input type="radio" name="rating" value="5" id="star5" required><label for="star5">★</label>
            <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
            <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
            <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
            <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
        </div>

        <textarea name="comment" rows="5" cols="50" placeholder="Write your review here..." required></textarea>
        <br><br>
        <button type="submit">Submit Review</button>
    </form>

    <br>
    <a href="view_reviews.php">See All Reviews</a>
</body>

</html>