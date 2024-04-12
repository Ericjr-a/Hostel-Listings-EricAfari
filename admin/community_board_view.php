<?php
session_start();
require 'connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

$hostelsQuery = "SELECT hostel_id, name FROM Hostels ORDER BY name ASC";
$hostelsResult = $conn->query($hostelsQuery);
$hostels = [];
while ($row = $hostelsResult->fetch_assoc()) {
    $hostels[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Community Board</title>
    <link rel="stylesheet" href="community_board.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#categorySelect").change(function() {
                var selectedCategory = $(this).val();
                $("#categoryForms > div").hide();
                $("#" + selectedCategory + "Form").show();
                fetchPosts(selectedCategory);
            });
        });



        function attachActionHandlers() {

        }


        function refreshDynamicEventHandlers() {

            attachActionHandlers();
        }

        function fetchPosts(category) {
            $.ajax({
                url: 'fetch_posts.php',
                type: 'POST',
                data: {
                    category: category
                },
                success: function(data) {

                    $('#postsDisplay').html(data);
                    attachActionHandlers();
                    $('.post').each(function() {
                        var postId = $(this).data('post-id');
                        $(this).append(`
                    <div class='post-actions'>
                        <button class='edit-post-btn' onclick='editPost(${postId});'>
                            <i class='fas fa-pencil-alt'></i>
                        </button>
                        <button class='delete-post-btn' onclick='deletePost(${postId});'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </div>`);
                    });
                }
            });
        }


        function showReplyForm(postId) {
            var formId = 'reply-form-' + postId;
            $('#' + formId).toggle();
        }

        function submitReply(postId) {
            var replyTextId = 'reply-text-' + postId;
            var replyText = $('#' + replyTextId).val();
            if (replyText.trim() === '') {
                alert('Please enter a reply.');
                return;
            }
            $.ajax({
                url: 'submit_reply.php',
                type: 'POST',
                data: {
                    post_id: postId,
                    reply_content: replyText
                },
                success: function() {
                    alert('Reply submitted successfully');
                    $('#' + replyTextId).val('');
                    fetchPosts($('#categorySelect').val());
                },
                error: function() {
                    alert('Error submitting reply');
                }
            });
        }



        function editReply(replyId) {
            var newContent = prompt("Edit your reply:");
            if (newContent) {
                $.ajax({
                    url: 'community_board_action.php',
                    type: 'POST',
                    data: {
                        action: 'editReply',
                        reply_id: replyId,
                        reply_content: newContent
                    },
                    success: function() {
                        alert('Reply edited successfully.');
                        //$('div[data-reply-id="' + replyId + '"] .reply-content').text(newContent);
                        fetchPosts($('#categorySelect').val());
                        //attachActionHandlers();


                    },
                    error: function() {
                        alert('Error editing reply.');
                    }
                });
            }
        }



        function deleteReply(replyId) {
            if (confirm("Are you sure you want to delete this reply?")) {
                $.ajax({
                    url: 'community_board_action.php',
                    type: 'POST',
                    data: {
                        action: 'deleteReply',
                        reply_id: replyId
                    },
                    success: function() {
                        alert('Reply deleted successfully.');
                        //$('div[data-reply-id="' + replyId + '"]').remove();
                        fetchPosts($('#categorySelect').val());
                        //attachActionHandlers();

                    },
                    error: function() {
                        alert('Error deleting reply.');
                    }
                });
            }
        }



        function editPost(postId) {
            var newContent = prompt("Edit your post:");
            if (newContent) {
                $.ajax({
                    url: 'community_board_action.php',
                    type: 'POST',
                    data: {
                        action: 'edit',
                        post_id: postId,
                        post_content: newContent
                    },
                    success: function() {
                        alert('Post edited successfully.');
                        //$('div[data-post-id="' + postId + '"] .post-content').text(newContent);
                        fetchPosts($('#categorySelect').val());

                    },
                    error: function() {
                        alert('Error editing post.');
                    }
                });
            }
        }


        function deletePost(postId) {
            if (!confirm("Are you sure you want to delete this post?")) {
                return;
            }

            $.ajax({
                url: 'community_board_action.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    post_id: postId
                },
                success: function() {
                    alert('Post deleted successfully.');
                    fetchPosts($('#categorySelect').val());
                },
                error: function() {
                    alert('Error deleting post.');
                }
            });
        }
    </script>

</head>

<body>
    <header>
        <div class="logo">Hostel-Listings</div>
        <nav class="nav-bar">
            <ul>
                <li><a href="welcome.php">Home</a></li>
                <li><a href="review_view.php">Reviews</a></li>
                <li><a href="profile_view.php">Profile Page</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Community Board</h1>
        <select id="categorySelect">
            <option value="">Select a category...</option>
            <option value="general">General Questions</option>
            <option value="roommate">Roommate Search</option>
            <option value="tips">Tips and Advice</option>
        </select>

        <div id="categoryForms">
            <div id="generalForm" style="display:none;">
                <form action="community_board_action.php" method="post">
                    <input type="hidden" name="action" value="post">
                    <input type="hidden" name="category" value="general">
                    <textarea name="post_content" placeholder="Your question here..." required></textarea>
                    <button type="submit">Post</button>
                </form>
            </div>


            <div id="roommateForm" style="display:none;">
                <form action="community_board_action.php" method="post">
                    <input type="hidden" name="action" value="post">
                    <input type="hidden" name="category" value="roommate">
                    <select name="hostel_id" required>
                        <option value="">Select Hostel...</option>
                        <?php foreach ($hostels as $hostel) : ?>
                            <option value="<?= $hostel['hostel_id'] ?>"><?= htmlspecialchars($hostel['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea name="post_content" placeholder="Describe your roommate preference..." required></textarea>
                    <button type="submit">Post</button>
                </form>
            </div>
            <div id="tipsForm" style="display:none;">
                <form action="community_board_action.php" method="post">
                    <input type="hidden" name="action" value="post">
                    <input type="hidden" name="category" value="tips">
                    <select name="hostel_id" required>
                        <option value="">Select Hostel for Tips...</option>
                        <?php foreach ($hostels as $hostel) : ?>
                            <option value="<?= $hostel['hostel_id'] ?>"><?= htmlspecialchars($hostel['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea name="post_content" placeholder="Share your tips or advice here..." required></textarea>
                    <button type="submit">Post</button>
                </form>
            </div>
        </div>
        <div id="postsDisplay">
        </div>
    </main>
</body>

</html>