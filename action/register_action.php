<?php
require_once 'connection.php';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        empty($_POST['first_name']) || empty($_POST['last_name']) ||
        empty($_POST['email']) || empty($_POST['contact_details']) ||
        empty($_POST['password']) || empty($_POST['date_of_birth']) ||
        $_POST['password'] !== $_POST['confirm_password']
    ) {
        header("Location: register_view.php?error=Please fill all the fields correctly.");
        exit();
    }

    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contactDetails = mysqli_real_escape_string($conn, $_POST['contact_details']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $dob = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $classYear = isset($_POST['class_year']) ? mysqli_real_escape_string($conn, $_POST['class_year']) : null;
    $userType = mysqli_real_escape_string($conn, $_POST['user_type']);

    if (!preg_match("/^[A-Za-z]+$/", $firstName)) {
        $errors['first_name'] = "First name should only contain letters.";
    }

    if (!preg_match("/^[A-Za-z]+$/", $lastName)) {
        $errors['last_name'] = "Last name should only contain letters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email address.";
    }

    if (strlen($contactDetails) != 10 || !ctype_digit($contactDetails)) {
        $errors['contact_details'] = "Contact details should be a 10-digit number.";
    }

    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if ($password !== $_POST['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match.";
    }


    if (!empty($errors)) {
        $query = http_build_query(['errors' => $errors]);
        header("Location: register_view.php?" . $query);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Users (first_name, last_name, email, contact_details, password, date_of_birth, class_year, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssssis", $firstName, $lastName, $email, $contactDetails, $hashedPassword, $dob, $classYear, $userType);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: login_view.php?registration=success');
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}
