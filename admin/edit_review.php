<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login_view.php');
    exit;
}

$reviewId = $_GET['review_id'] ?? 0;

$reviewQuery = $conn->prepare("SELECT review_text, user_id FROM Reviews WHERE review_id = ?");
$reviewQuery->bind_param("i", $reviewId);
$reviewQuery->execute();
$reviewResult = $reviewQuery->get_result();
if ($reviewResult->num_rows === 0) {
    echo "Review not found.";
    exit;
}
$review = $reviewResult->fetch_assoc();

if ($_SESSION['user_id'] != $review['user_id']) {
    echo "You do not have permission to edit this review.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewText = $_POST['review_text'];
    $updateQuery = $conn->prepare("UPDATE Reviews SET review_text = ? WHERE review_id = ?");
    $updateQuery->bind_param("si", $reviewText, $reviewId);
    $updateQuery->execute();

    header('Location: review_view.php?hostel_id=' . $hostelId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="register_view.css">
    <title>Edit Review</title>
</head>

<body>
    <h1>Edit Your Review</h1>
    <form action="" method="post">
        <textarea name="review_text" required><?php echo htmlspecialchars($review['review_text']); ?></textarea>
        <button type="submit">Update Review</button>
    </form>
</body>

</html>