<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login_view.css">
</head>

<body>
    <div class="form-container" id="loginFormContainer">
        <h2>Login</h2>
        <form action="login_user_action.php" method="post">
            <div class="input-group">
                <input type="email" id="emailLogin" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" id="passwordLogin" name="password" placeholder="Password" required>
            </div>
            <?php if (isset($_GET['error'])) : ?>
                <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <div class="input-group">
                <button type="submit" id="signInButton">Sign In</button>
            </div>
            <div class="register-link">
                <a href="register_view.php">Register</a>
            </div>
        </form>
    </div>
</body>

</html>