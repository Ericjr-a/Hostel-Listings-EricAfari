<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "User not logged in.";
    exit;
}

if (!isset($_POST['post_id'], $_POST['reply_content']) || empty($_POST['reply_content'])) {
    echo "Missing reply content or post ID.";
    exit;
}

$post_id = $_POST['post_id'];
$reply_content = $_POST['reply_content'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO Replies (post_id, user_id, reply_content) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $user_id, $reply_content);

if ($stmt->execute()) {
    echo "Reply successfully added.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
