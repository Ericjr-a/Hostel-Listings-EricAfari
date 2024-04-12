<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
    $userId = $_SESSION['user_id'];

    $sql = "DELETE FROM CommunityPosts WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ii', $postId, $userId);
        if ($stmt->execute()) {
            header('Location: community_board_view.php');
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No post ID provided.";
}
