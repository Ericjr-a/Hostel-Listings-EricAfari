<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register_view.css">

    <title>Sign Up</title>
</head>

<body>
    <div>
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="register_action.php" method="post">
            <div>
                <label>Name</label>
                <input type="text" name="name" value="<?php echo $name; ?>">
                <span><?php echo $name_err; ?></span>
            </div>
            <div>
                <label>Email</label>
                <input type="text" name="email" value="<?php echo $email; ?>">
                <span><?php echo $email_err; ?></span>
            </div>
            <div>
                <label>Contact Details</label>
                <input type="text" name="contact_details" value="<?php echo $contact_details; ?>">
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" value="<?php echo $password; ?>">
                <span><?php echo $password_err; ?></span>
            </div>
            <div>
                <label>Verify Password</label>
                <input type="password" name="confirm_password" value="">
                <span><?php echo $confirm_password_err; ?></span>
            </div>

            <div>
                <label>Class Year (Optional)</label>
                <input type="number" name="class_year" value="<?php echo $class_year; ?>">
            </div>
            <div>
                <label>User Type</label>
                <select name="user_type">
                    <option value="student">Student</option>
                    <option value="resident_assistant">Resident Assistant</option>
                </select>
                <span><?php echo $user_type_err; ?></span>
            </div>
            <div>
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>

</html>