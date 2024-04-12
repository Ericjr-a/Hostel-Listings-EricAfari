<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login_view.php');
    exit;
}

$reviewId = $_GET['review_id'] ?? 0;

$reviewQuery = $conn->prepare("SELECT user_id FROM Reviews WHERE review_id = ?");
$reviewQuery->bind_param("i", $reviewId);
$reviewQuery->execute();
$reviewResult = $reviewQuery->get_result();
if ($reviewResult->num_rows === 0) {
    echo "Review not found.";
    exit;
}
$review = $reviewResult->fetch_assoc();

if ($_SESSION['user_id'] != $review['user_id']) {
    echo "You do not have permission to delete this review.";
    exit;
}

$deleteQuery = $conn->prepare("DELETE FROM Reviews WHERE review_id = ?");
$deleteQuery->bind_param("i", $reviewId);
$deleteQuery->execute();

header('Location: review_view.php?hostel_id=' . $hostelId);
exit;
