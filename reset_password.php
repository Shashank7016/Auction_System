<?php
// Student name: Shashank Chauhan
// The main purpose of this file is to handle password resetting

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password.php?token=$token");
        exit();
    }

    $xml = simplexml_load_file('customer.xml');
    if ($xml === false) {
        die("Error loading XML file.");
    }

    $tokenValid = false;
    foreach ($xml->customer as $customer) {
        if ((string) $customer->resetToken === $token) {
            $tokenValid = true;
            $salt = '$6$' . bin2hex(random_bytes(8)) . '$'; // Using SHA-512 for hashing
            $hashedPassword = crypt($newPassword, $salt);
            $customer->password = $hashedPassword;
            unset($customer->resetToken); // Remove the reset token
            $xml->asXML('customer.xml');
            echo "Password has been reset successfully.";
            break;
        }
    }

    if (!$tokenValid) {
        $_SESSION['error'] = "Invalid or expired token.";
        header("Location: forgot_password.php");
        exit();
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Reset Password</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <div class="form-container">
                <h2>Reset Password</h2>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <form action="reset_password.php" method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Reset Password" class="btn">
                    </div>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "Invalid request.";
}
?>
