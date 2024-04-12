<?php
session_start();
require_once "connection.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("location: welcome.php");
    exit;
}

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]) ?? '';
    $password = trim($_POST["password"]) ?? '';

    if (empty($email)) {
        $email_err = "Please enter your email.";
    }

    if (empty($password)) {
        $password_err = "Please enter your password.";
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT user_id, email, password, user_type, first_name FROM Users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $email, $hashed_password, $user_type, $first_name);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_regenerate_id();

                            $_SESSION['logged_in'] = true;
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['email'] = $email;
                            $_SESSION['user_type'] = $user_type;
                            $_SESSION['first_name'] = $first_name;

                            header("location: welcome.php");
                            exit;
                        } else {
                            header("location: login_view.php?error=Invalid email or password.");
                            exit;
                        }
                    }
                } else {
                    header("location: login_view.php?error=Invalid email or password.");
                    exit;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
