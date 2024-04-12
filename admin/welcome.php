<?php
require 'connection.php';

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit();
}


$hostelsQuery = "SELECT Hostels.*, Images.image_path AS image_path FROM Hostels 
                 LEFT JOIN Images ON Hostels.hostel_id = Images.hostel_id 
                 ORDER BY RAND() LIMIT 5";
$hostelsResult = mysqli_query($conn, $hostelsQuery);



if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']) {
    $first_name = $_SESSION['first_name'];
    $_SESSION['profile_updated'] = false;
} else {
    $first_name = $_SESSION['first_name'] ?? 'Guest';
}
$user_type = $_SESSION['user_type'] ?? 'unknown';
$first_name = $_SESSION['first_name'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="welcome.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Welcome | Hostel-Listings</title>
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
        <div class="nav-bar">
            <ul>
                <li><a href="review_view.php">Reviews</a></li>
                <li><a href="community_board_view.php">Community Board</a></li>
                <li><a href="profile_view.php">Profile Page</a></li>

                <li>
                    <form method="get" class="search-bar">
                        <input type="search" name="query" placeholder="Search hostels...">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <main>
        <div class="hero">
            <div class="content">
                <h1>Welcome to Hostel-Listings, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>
                <h3>Find the perfect place to stay.</h3>
            </div>
        </div>

        <div class="display_hostels">
            <h2>Featured Hostels</h2>
            <div class="box">
                <?php while ($hostel = mysqli_fetch_assoc($hostelsResult)) : ?>
                    <div class="card">
                        <h3>Name: <?php echo htmlspecialchars($hostel['name']); ?></h3>
                        <p>Address: <?php echo htmlspecialchars($hostel['address']); ?></p>
                        <p>Number of rooms: <?php echo htmlspecialchars($hostel['number_of_rooms']); ?></p>
                        <?php
                        $defaultImagePath = "path/to/default_hostel_image.jpg";
                        $imagePath = isset($hostel['image_path']) && !empty($hostel['image_path'])
                            ? "/hostel_images/" . htmlspecialchars($hostel['image_path'])
                            : $defaultImagePath;
                        ?>
                        <img src="<?php echo $imagePath; ?>" alt="Image of <?php echo htmlspecialchars($hostel['name']); ?>" style="width:100%;">
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
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
<?php
mysqli_close($conn);
?>