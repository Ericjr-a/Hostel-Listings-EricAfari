<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register | Hostel-Listings</title>
    <link rel="stylesheet" href="register_view.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register_action.php" method="POST">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="first_name" title="First name should only contain letters." required />

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="last_name" title="Last name should only contain letters." required />

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" title="Enter a valid email address." required />

            <label for="contactDetails">Contact Details:</label>
            <input type="text" id="contactDetails" name="contact_details" title="Contact details should only contain numbers." required />

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirm_password" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="date_of_birth" required>

            <label for="classYear">Class Year:</label>
            <input type="number" id="classYear" name="class_year">

            <label>Are you a Student or a Resident Assistant?:</label>
            <select name="user_type" required>
                <option value="student">Student</option>
                <option value="resident_assistant">Resident Assistant</option>
            </select>

            <button type="submit">Register</button>
            <div class="login-redirect">
                Already registered? <a href="login_view.php">Log in</a>
            </div>
        </form>
    </div>
    <script>
        function preventNumberInput(event) {
            var charCode = event.charCode || event.keyCode;
            if (charCode >= 48 && charCode <= 57) {
                event.preventDefault();
            }
        }

        function preventLetterInput(event) {
            var charCode = event.charCode || event.keyCode;
            if ((charCode < 48 || charCode > 57) && charCode !== 43) {
                event.preventDefault();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('firstName').addEventListener('keypress', preventNumberInput);
            document.getElementById('lastName').addEventListener('keypress', preventNumberInput);
            document.getElementById('contactDetails').addEventListener('keypress', preventLetterInput);
        });
    </script>
</body>

</html>