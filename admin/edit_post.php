<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postId = $_POST['post_id'] ?? '';
    $postContent = $_POST['post_content'] ?? '';
    $userId = $_SESSION['user_id'];

    $sql = "UPDATE CommunityPosts SET post_content = ? WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('sii', $postContent, $postId, $userId);
        if ($stmt->execute()) {
            header('Location: community_board_view.php');
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    $postId = $_GET['post_id'] ?? '';

    $sql = "SELECT post_content FROM CommunityPosts WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $postId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $postContent = $row['post_content'] ?? '';
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Edit Post</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0;
                padding: 20px;
            }

            form {
                background-color: #ffffff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                max-width: 500px;
                margin: 0 auto;
            }

            textarea {
                width: 100%;
                height: 150px;
                margin-bottom: 20px;
                padding: 10px;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
            }

            button {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }

            button:hover {
                background-color: #45a049;
            }

            input[type="hidden"] {
                display: none;
            }
        </style>
    </head>

    <body>
        <form action="edit_post.php" method="post">
            <input type="hidden" name="post_id" value="<?= htmlspecialchars($postId) ?>">
            <textarea name="post_content"><?= htmlspecialchars($postContent) ?></textarea>
            <button type="submit">Save Changes</button>
        </form>
    </body>

    </html>

<?php
}
?>