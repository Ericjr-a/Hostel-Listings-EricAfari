<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

$hostelsQuery = "
SELECT Hostels.hostel_id, Hostels.name, Hostels.address, Images.image_path 
FROM Hostels 
LEFT JOIN Images ON Hostels.hostel_id = Images.hostel_id 
ORDER BY Hostels.name ASC";
$hostelsResult = $conn->query($hostelsQuery);

$selectedHostelId = isset($_GET['hostel_id']) ? $_GET['hostel_id'] : null;
$reviewsResult = null;
$selectedHostelName = '';

if ($selectedHostelId) {
    $selectedHostelQuery = $conn->prepare("SELECT name FROM Hostels WHERE hostel_id = ?");
    $selectedHostelQuery->bind_param("i", $selectedHostelId);
    $selectedHostelQuery->execute();
    $result = $selectedHostelQuery->get_result();
    if ($row = $result->fetch_assoc()) {
        $selectedHostelName = $row['name'];
    }

    $reviewsQuery = $conn->prepare("
    SELECT Reviews.*, Users.first_name, Users.last_name, Users.user_type 
    FROM Reviews 
    JOIN Users ON Reviews.user_id = Users.user_id 
    WHERE Reviews.hostel_id = ? 
    ORDER BY Reviews.review_id DESC");
    $reviewsQuery->bind_param("i", $selectedHostelId);
    $reviewsQuery->execute();
    $reviewsResult = $reviewsQuery->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Reviews</title>
    <link rel="stylesheet" href="review_view.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var originalContent = $('.box').html();

            $('.search-bar').submit(function(event) {
                event.preventDefault();
                var searchQuery = $('input[type="search"]').val().trim();

                if (searchQuery.length >= 4) {
                    $.ajax({
                        url: 'search_results.php',
                        type: 'GET',
                        data: {
                            query: searchQuery
                        },
                        success: function(data) {
                            $('.box').html(data);
                        },
                        error: function(xhr, status, error) {
                            console.error("An error occurred during the search: ", status, error);
                        }
                    });
                } else {
                    alert("Please enter at least 4 characters to search.");
                }
            });

            $('input[type="search"]').on('input', function() {
                if (!this.value) {
                    $('.box').html(originalContent);
                }
            });
        });
    </script>


</head>

<body>
    <header>
        <div class="logo">Hostel-Listings</div>
        <nav class="nav-bar">
            <ul>
                <li><a href="welcome.php">Home</a></li>
                <li><a href="community_board_view.php">Community Board</a></li>
                <li><a href="profile_view.php">Profile Page</a></li>
                <li>
                    <form method="get" class="search-bar">
                        <input type="search" name="query" placeholder="Search hostels...">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>



    <div class="hero">
        <div class="content">
            <h1>Hostel Reviews</h1>
            <h2>Click on the name of the hostel to post a review and see it's reviews from others</h2>
        </div>
    </div>



    <div class="display_hostels">
        <div class="box">
            <?php while ($hostel = $hostelsResult->fetch_assoc()) : ?>
                <div class="card">
                    <a href="?hostel_id=<?= htmlspecialchars($hostel['hostel_id']); ?>">
                        Name: <?= htmlspecialchars($hostel['name']); ?>
                    </a>
                    <?php if (!empty($hostel['image_path'])) : ?>
                        <img src="/hostel_images/<?= htmlspecialchars($hostel['image_path']); ?>" alt="Hostel Image">
                    <?php endif; ?>
                    <p><?= htmlspecialchars($hostel['address']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>



    <?php if ($selectedHostelId && $reviewsResult) : ?>
        <div class="reviews_section">
            <h3>Reviews for <?= htmlspecialchars($selectedHostelName); ?></h3>
            <form action="review_action.php" method="post">
                <input type="hidden" name="action" value="post_review">
                <input type="hidden" name="hostel_id" value="<?= htmlspecialchars($selectedHostelId); ?>">
                <textarea name="review_text" placeholder="Write your review here..." required></textarea>
                <button type="submit">Submit Review</button>
            </form>
            <div class="box">
                <?php while ($review = $reviewsResult->fetch_assoc()) : ?>
                    <div class="card">
                        <p><?= htmlspecialchars($review['first_name'] . " " . $review['last_name'] . " (" . ucfirst($review['user_type']) . ")"); ?></p>
                        <div class="review-content">
                            <p><?= htmlspecialchars($review['review_text']); ?></p>
                            <div class="review-actions">
                                <?php if ($_SESSION['user_id'] == $review['user_id']) : ?>
                                    <a href="edit_review.php?review_id=<?= htmlspecialchars($review['review_id']); ?>&hostel_id=<?= htmlspecialchars($selectedHostelId); ?>" class="edit-button">Edit</a>
                                    <a href="delete_review.php?review_id=<?= htmlspecialchars($review['review_id']); ?>&hostel_id=<?= htmlspecialchars($selectedHostelId); ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>



    <?php elseif ($selectedHostelId) : ?>
        <div class="reviews_section">
            <p>No reviews found for <?= htmlspecialchars($selectedHostelName); ?>. Be the first to leave a review!</p>
            <form action="review_action.php" method="post">
                <input type="hidden" name="action" value="post_review">
                <input type="hidden" name="hostel_id" value="<?= htmlspecialchars($selectedHostelId); ?>">
                <textarea name="review_text" placeholder="Write your review here..." required></textarea>
                <button type="submit">Submit Review</button>
            </form>
        </div>

    <?php endif; ?>
    <footer>
        <p>Hospitals available 24/7: Sibakom Hospital and Ashongman Community Hospital</p>
        <p>Emergency Health line - 0501331668. Call this number if the hostel manager is not present</p>
        <p>Working Hours:</p>
        <ul>
            <li>Monday to Friday: 8am to 8pm (DAY), 8pm to 8am (NIGHT)</li>
            <li>Saturday to Sunday: 9am to 5pm (DAY), 8pm to 8am (NIGHT)</li>
        </ul>
    </footer>
</body>



</html>