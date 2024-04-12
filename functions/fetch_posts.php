<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

$category = $_POST['category'] ?? '';

if (!in_array($category, ['general', 'roommate', 'tips'])) {
    echo "Invalid category.";
    exit;
}

$query = "SELECT CommunityPosts.*, Users.first_name, Users.last_name, Hostels.name AS hostel_name FROM CommunityPosts 
          LEFT JOIN Users ON CommunityPosts.user_id = Users.user_id 
          LEFT JOIN Hostels ON CommunityPosts.hostel_id = Hostels.hostel_id 
          WHERE category = ? ORDER BY post_id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();











if ($result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        echo "<div class='post' data-post-id='" . htmlspecialchars($post['post_id']) . "'>";
        echo "<h3>" . htmlspecialchars($post['first_name']) . " " . htmlspecialchars($post['last_name']);
        if (!empty($post['hostel_name'])) {
            echo " (Hostel: " . htmlspecialchars($post['hostel_name']) . ")";
        }
        echo "</h3>";
        echo "<p>" . nl2br(htmlspecialchars($post['post_content'])) . "</p>";
        echo "<button onclick='showReplyForm(" . $post['post_id'] . ")'>Reply</button>";
        echo "<div id='reply-form-" . $post['post_id'] . "' style='display:none;'>";
        echo "<textarea id='reply-text-" . $post['post_id'] . "'></textarea>";
        echo "<button onclick='submitReply(" . $post['post_id'] . ")'>Submit Reply</button>";
        echo "</div>";

        $replyQuery = "SELECT Replies.*, Users.first_name, Users.last_name FROM Replies 
                       JOIN Users ON Replies.user_id = Users.user_id 
                       WHERE Replies.post_id = ? ORDER BY Replies.reply_id ASC";
        $replyStmt = $conn->prepare($replyQuery);
        $replyStmt->bind_param("i", $post['post_id']);
        $replyStmt->execute();
        $repliesResult = $replyStmt->get_result();

        while ($reply = $repliesResult->fetch_assoc()) {
            echo "<div class='reply' data-reply-id='" . htmlspecialchars($reply['reply_id']) . "'>";
            echo "<div class='reply-content'>";
            echo "<strong>" . htmlspecialchars($reply['first_name']) . " " . htmlspecialchars($reply['last_name']) . "</strong>: ";
            echo nl2br(htmlspecialchars($reply['reply_content']));
            echo "</div>";

            if ($_SESSION['user_id'] == $reply['user_id']) {
                echo "<div class='reply-actions'>";
                echo "<button class='edit-reply-btn' onclick='editReply(" . $reply['reply_id'] . ")'><i class='fas fa-pencil-alt'></i></button>";
                echo "<button class='delete-reply-btn' onclick='deleteReply(" . $reply['reply_id'] . ")'><i class='fas fa-trash'></i></button>";
                echo "</div>";
            }

            echo "</div>";
        }



        echo "</div>";
    }
} else {
    echo "No posts found in this category.";
}

$conn->close();
