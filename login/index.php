<?php
require 'connection.php';

$query = "SELECT Hostels.*, Images.image_path FROM Hostels LEFT JOIN Images ON Hostels.hostel_id = Images.hostel_id";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to Hostel Listings</title>
    <link rel="stylesheet" href="index.css">

</head>

<body>
    <header>
        <div class="logo">
            Hostel Listings
        </div>
        <nav>
            <ul>
                <li><a href="register_view.php">Sign Up</a></li>
                <li><a href="login_view.php">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="intro">
            <p>Welcome to <strong>Hostel Listings</strong>! Explore the hostels available on our platform. To access all features, please<a href="register_view.php">sign up</a> or <a href="login_view.php">login</a></p>
        </section>

        <section class="hostels">
            <h2>Available Hostels</h2>
            <div class="hostel-list">
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($hostel = $result->fetch_assoc()) : ?>
                        <div class="hostel">
                            <h3>Name:<?= htmlspecialchars($hostel['name']); ?></h3>
                            <p>Location: <?= htmlspecialchars($hostel['address']); ?></p>
                            <p>Number of rooms: <?php echo htmlspecialchars($hostel['number_of_rooms']); ?></p>

                            <?php if (!empty($hostel['image_path'])) : ?>
                                <img src="/hostel_images/<?= htmlspecialchars($hostel['image_path']); ?>" alt="<?= htmlspecialchars($hostel['name']); ?>" class="hostel-image">
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>No hostels found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; <?= date("Y"); ?> Hostel Listings. All rights reserved.</p>
    </footer>
</body>

</html>