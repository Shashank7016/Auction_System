<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
    <?php
    //The main purpose of this file is to allow user to login if registration is successful
    session_start();
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }

    if (isset($_GET['error'])) {
        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
    }

    
    if (isset($_GET['register'])) {
        echo '
        <h2>Register</h2>
        <form action="register.php" method="post">
            <div class="form-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="surname">Surname:</label>
                        <input type="text" id="surname" name="surname" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="register" value="Register" class="btn">
                    </div>
                </form>';
    } else {
        ?>
        <h2>Login</h2>
        <form action="register.php" method="post">
        <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="login" value="Login" class="btn">
                    </div>
                </form>
                <div class="register-link">
                    <a href="login.php?register=1">Register as a new user</a>
                </div>
        <?php
    }
    ?>
     </div>
     </div>
    <script src="js/scripts.js"></script>
</body>
</html>
