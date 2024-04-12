<?php
session_start();
require 'connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre>POST Data: ";
print_r($_POST);
echo "</pre>";

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login_view.php');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        $userId = $_SESSION['user_id'];
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $email = $_POST['email'];
        $contactDetails = $_POST['contact_details'];

        $updateQuery = "UPDATE Users SET first_name = ?, last_name = ?, email = ?, contact_details = ? WHERE user_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("ssssi", $firstName, $lastName, $email, $contactDetails, $userId);
            if ($stmt->execute()) {
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name'] = $lastName;
                $_SESSION['email'] = $email;
                $_SESSION['contact_details'] = $contactDetails;

                header('Location: profile_view.php?profile_updated=true');
            } else {
                echo "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
        break;

    case 'add_hostel':
        if (!isset($_POST['hostel_name'], $_POST['hostel_address'], $_POST['number_of_rooms']) || !isset($_FILES['hostel_image'])) {
            echo "Error: Form data for adding hostel is incomplete.";
            exit;
        }

        $hostelName = $_POST['hostel_name'];
        $hostelAddress = $_POST['hostel_address'];
        $numberOfRooms = $_POST['number_of_rooms'];
        $residentAssistantId = $_SESSION['user_id'];

        $imageName = $_FILES['hostel_image']['name'];
        $imageType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $tempName = $_FILES['hostel_image']['tmp_name'];
        $folder = "/var/www/html/hostel_images/" . basename($imageName);

        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageType, $allowedTypes)) {
            echo "Error: Only JPG, JPEG, and PNG files are allowed.";
            exit;
        }

        if ($_FILES['hostel_image']['size'] > 5000000) {
            echo "Error: Your file is too large. Maximum size is 5MB.";
            exit;
        }

        if (!is_dir("/var/www/html/hostel_images/") || !is_writable("/var/www/html/hostel_images/")) {
            echo "Error: Destination directory is not writable or does not exist.";
            exit;
        }

        $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        try {
            $insertHostelQuery = "INSERT INTO Hostels (resident_assistant_id, name, address, number_of_rooms) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertHostelQuery);
            $stmt->bind_param("issi", $residentAssistantId, $hostelName, $hostelAddress, $numberOfRooms);
            $stmt->execute();
            $hostelId = $conn->insert_id;

            if (!move_uploaded_file($tempName, $folder)) {
                throw new Exception("Failed to upload image. Check directory permissions and path.");
            }

            $insertImageQuery = "INSERT INTO Images (hostel_id, image_path) VALUES (?, ?)";
            $stmt = $conn->prepare($insertImageQuery);
            $stmt->bind_param("is", $hostelId, $imageName);
            $stmt->execute();

            $conn->commit();
            header('Location: profile_view.php?hostel_added=true');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            exit;
        }
        break;


    case 'edit_hostel':
        if (!isset($_POST['hostel_id'], $_POST['hostel_name'], $_POST['hostel_address'], $_POST['number_of_rooms'])) {
            echo "Error: Missing information to update the hostel.";
            exit;
        }

        $hostelId = $_POST['hostel_id'];
        $hostelName = $_POST['hostel_name'];
        $hostelAddress = $_POST['hostel_address'];
        $numberOfRooms = $_POST['number_of_rooms'];

        $updateHostelQuery = "UPDATE Hostels SET name = ?, address = ?, number_of_rooms = ? WHERE hostel_id = ?";
        if ($stmt = $conn->prepare($updateHostelQuery)) {
            $stmt->bind_param("ssii", $hostelName, $hostelAddress, $numberOfRooms, $hostelId);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error updating hostel details: " . $conn->error;
            exit;
        }

        if (isset($_FILES['new_hostel_image']) && $_FILES['new_hostel_image']['error'] == 0) {
            $imageName = $_FILES['new_hostel_image']['name'];
            $imageType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $tempName = $_FILES['new_hostel_image']['tmp_name'];
            $folder = "/var/www/html/hostel_images/" . basename($imageName);

            $allowedTypes = ['jpg', 'jpeg', 'png'];
            if (!in_array($imageType, $allowedTypes)) {
                echo "Error: Only JPG, JPEG, and PNG files are allowed.";
                exit;
            }

            if ($_FILES['new_hostel_image']['size'] > 5000000) {
                echo "Error: Your file is too large. Maximum size is 5MB.";
                exit;
            }

            if (move_uploaded_file($tempName, $folder)) {
                $updateImageQuery = "UPDATE Images SET image_path = ? WHERE hostel_id = ?";
                if ($stmt = $conn->prepare($updateImageQuery)) {
                    $stmt->bind_param("si", $imageName, $hostelId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "Error updating image: " . $conn->error;
                }
            } else {
                echo "Error uploading new image.";
                exit;
            }
        }

        header('Location: profile_view.php?hostel_edited=true');
        exit;
        break;



    case 'delete_hostel':
        $hostelId = isset($_POST['hostel_id']) ? (int)$_POST['hostel_id'] : 0;

        if ($hostelId > 0) {
            $deleteImagesQuery = "DELETE FROM Images WHERE hostel_id = ?";
            if ($stmt = $conn->prepare($deleteImagesQuery)) {
                $stmt->bind_param("i", $hostelId);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error deleting images: " . $conn->error;
            }

            $deleteHostelQuery = "DELETE FROM Hostels WHERE hostel_id = ?";
            if ($stmt = $conn->prepare($deleteHostelQuery)) {
                $stmt->bind_param("i", $hostelId);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error deleting hostel: " . $conn->error;
            }

            header('Location: profile_view.php');
        } else {
            echo "Invalid hostel ID.";
        }
        break;





    case 'change_password':
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'];
        $email = $_POST['email'];
        $newPassword = $_POST['new_password'];

        $userQuery = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
        $userQuery->bind_param("i", $userId);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        $user = $userResult->fetch_assoc();

        if ($email === $user['email'] && password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordQuery = "UPDATE Users SET password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updatePasswordQuery);
            $stmt->bind_param("si", $hashedPassword, $userId);
            if ($stmt->execute()) {
                echo "<script>alert('Password changed successfully.');</script>";
            } else {
                echo "<script>alert('Error updating password.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Credential verification failed. Password not changed.');</script>";
        }
        echo "<script>window.location.href = 'profile_view.php';</script>";
        break;







    default:
        echo "The action received was: '{$action}'";
        break;
}





$conn->close();
