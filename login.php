<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    //The main purpose of this file is to allow user to login if registration is successful
    session_start();
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">' 
        . $_SESSION['error'] . 
        '</div>';
        unset($_SESSION['error']);
    }
    
    if (isset($_GET['register'])) {
        echo '
        <form action="register.php" method="post">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required>
            <br>
            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <br>
            <input type="submit" name="register" value="Register">
        </form>';
    } else {
        ?>
        <form action="register.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" name="login" value="Login">
        </form>

        <br>
        <a href="login.php?register=1">Register as a new user</a>
        <?php
    }
    ?>
    <script src="js/scripts.js"></script>
</body>
</html>
