<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

require 'connection.php';


if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}
$userId = $_SESSION['user_id'];
$userQuery = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

$fetchHostelsQuery = "SELECT Hostels.*, Images.image_path FROM Hostels LEFT JOIN Images ON Hostels.hostel_id = Images.hostel_id WHERE Hostels.resident_assistant_id = ?";
$hostelsStmt = $conn->prepare($fetchHostelsQuery);
$hostelsStmt->bind_param("i", $userId);
$hostelsStmt->execute();
$hostelsResult = $hostelsStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile | Hostel-Listings</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('profile_updated')) {
                alert('Your information has been updated successfully.');
            }
            if (urlParams.has('hostel_edited')) {
                alert('Your hostel information has been edited successfully.');
            }
            if (urlParams.has('hostel_added')) {
                alert('This hostel has been added successfully.');
            }

        });

        function togglePasswordVisibility() {
            var x = document.getElementById("currentPassword");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>















</head>

<body>
    <h1>Profile Management</h1>

    <form action="profile_action.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile">
        <div><label>First Name:</label><input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required></div>
        <div><label>Last Name:</label><input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required></div>
        <div><label>Email:</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
        <div><label>Contact Details:</label><input type="text" name="contact_details" value="<?= htmlspecialchars($user['contact_details']) ?>" required></div>
        <button type="submit">Update Profile</button>
    </form>

    <h2>Change Password</h2>
    <form action="profile_action.php" method="post" id="changePasswordForm">
        <input type="hidden" name="action" value="change_password">
        <div>
            <label>Current Password:</label>
            <input type="password" name="current_password" id="currentPassword" required>
            <button type="button" onclick="togglePasswordVisibility()">Show</button>
        </div>
        <div><label>Email:</label><input type="email" name="email" required></div>
        <div><label>New Password:</label><input type="password" name="new_password" required></div>
        <button type="submit">Change Password</button>
    </form>




    <?php if ($_SESSION['user_type'] == 'resident_assistant') : ?>
        <h2>Manage Hostels</h2>
        <form id="addHostelForm" action="profile_action.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_hostel">
            <div>
                <label for="hostel_name">Hostel Name:</label>
                <input type="text" id="hostel_name" name="hostel_name" required>
            </div>
            <div>
                <label for="hostel_address">Address:</label>
                <input type="text" id="hostel_address" name="hostel_address" required>
            </div>
            <div>
                <label for="number_of_rooms">Number of Rooms:</label>
                <input type="number" id="number_of_rooms" name="number_of_rooms" required>
            </div>
            <div>
                <label for="hostel_image">Hostel Image:</label>
                <input type="file" id="hostel_image" name="hostel_image">
            </div>
            <button type="submit">Add Hostel</button>
        </form>





        <?php while ($hostel = $hostelsResult->fetch_assoc()) : ?>
            <div class="hostel">
                <form action="profile_action.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_hostel">
                    <input type="hidden" name="hostel_id" value="<?= $hostel['hostel_id'] ?>">
                    <div><label>Hostel Name:</label><input type="text" name="hostel_name" value="<?= htmlspecialchars($hostel['name']) ?>" required></div>
                    <div><label>Address:</label><input type="text" name="hostel_address" value="<?= htmlspecialchars($hostel['address']) ?>" required></div>
                    <div><label>Number of Rooms:</label><input type="number" name="number_of_rooms" value="<?= $hostel['number_of_rooms'] ?>" required></div>
                    <div>
                        <label>Current Image:</label>
                        <p><img src="/hostel_images/<?= htmlspecialchars($hostel['image_path']) ?>" alt="Hostel Image" style="width: 100px;"></p>
                        <label>New Image (optional):</label>
                        <input type="file" name="new_hostel_image">
                    </div>
                    <button type="submit" class="btn-icon"><i class="fas fa-pencil-alt"></i> Edit</button>
                </form>
                <form action="profile_action.php" method="post" onsubmit="return confirmDelete();">
                    <input type="hidden" name="action" value="delete_hostel">
                    <input type="hidden" name="hostel_id" value="<?= $hostel['hostel_id'] ?>">
                    <button type="submit" class="btn-icon"><i class="fas fa-trash"></i> Delete</button>
                </form>
                <script>
                    function confirmDelete() {
                        return confirm('Are you sure you want to delete this hostel?');
                    }
                </script>

            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <form action="logout.php" method="post"><button type="submit">Logout</button></form>
    <form action="welcome.php" method="get"><button type="submit">Done</button></form>




</body>

</html>