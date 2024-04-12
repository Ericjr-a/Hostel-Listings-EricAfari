<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'post_review':
        $userId = $_SESSION['user_id'];
        $hostelId = $_POST['hostel_id'];
        $reviewText = $_POST['review_text'];

        $insertQuery = $conn->prepare("INSERT INTO Reviews (hostel_id, user_id, review_text) VALUES (?, ?, ?)");
        $insertQuery->bind_param("iis", $hostelId, $userId, $reviewText);
        $insertQuery->execute();

        header('Location: review_view.php');
        break;

    case 'edit_review':
        $reviewId = $_POST['review_id'];
        $reviewText = $_POST['review_text'];
        $hostelId = $_POST['hostel_id'];

        $updateQuery = $conn->prepare("UPDATE Reviews SET review_text = ? WHERE review_id = ? AND user_id = ?");
        $updateQuery->bind_param("sii", $reviewText, $reviewId, $_SESSION['user_id']);
        $updateQuery->execute();

        header('Location: review_view.php');
        break;

    case 'delete_review':
        $reviewId = $_POST['review_id'];
        $hostelId = $_POST['hostel_id'];

        $deleteQuery = $conn->prepare("DELETE FROM Reviews WHERE review_id = ? AND user_id = ?");
        $deleteQuery->bind_param("ii", $reviewId, $_SESSION['user_id']);
        $deleteQuery->execute();

        header('Location: review_view.php');
        break;
}

$conn->close();
