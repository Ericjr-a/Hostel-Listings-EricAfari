<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'post':
        handlePost($conn);
        break;
    case 'edit':
        handleEdit($conn);
        break;
    case 'delete':
        handleDelete($conn);
        break;
    case 'editReply':
        handleEditReply($conn);
        break;
    case 'deleteReply':
        handleDeleteReply($conn);
        break;
    default:
        echo "Invalid action.";
        break;
}

$conn->close();

function handlePost($conn)
{
    $category = $_POST['category'];
    $postContent = $_POST['post_content'];
    $userId = $_SESSION['user_id'];
    $hostelId = null;
    if (in_array($category, ['roommate', 'tips'])) {
        $hostelId = $_POST['hostel_id'] ?? null;
    }

    $stmt = $conn->prepare("INSERT INTO CommunityPosts (user_id, category, post_content, hostel_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $category, $postContent, $hostelId);

    if ($stmt->execute()) {
        header('Location: community_board_view.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
function handleEdit($conn)
{
    $postId = $_POST['post_id'];
    $postContent = $_POST['post_content'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE CommunityPosts SET post_content = ? WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $postContent, $postId, $userId);

    if ($stmt->execute()) {
        header('Location: community_board_view.php');
    } else {
        echo "Error editing post: " . $stmt->error;
    }
}
function handleDelete($conn)
{
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM CommunityPosts WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $postId, $userId);

    if ($stmt->execute()) {
        header('Location: community_board_view.php');
    } else {
        echo "Error deleting post: " . $stmt->error;
    }
}


function handleEditReply($conn)
{
    $replyId = $_POST['reply_id'];
    $newContent = $_POST['reply_content'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE Replies SET reply_content = ? WHERE reply_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $newContent, $replyId, $userId);

    if ($stmt->execute()) {
        echo "Reply updated successfully.";
    } else {
        echo "Error updating reply: " . $stmt->error;
    }
}

function handleDeleteReply($conn)
{
    $replyId = $_POST['reply_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM Replies WHERE reply_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $replyId, $userId);

    if ($stmt->execute()) {
        echo "Reply deleted successfully.";
    } else {
        echo "Error deleting reply: " . $stmt->error;
    }
}
