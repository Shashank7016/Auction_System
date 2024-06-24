<?php
// Student name: Shashank Chauhan
// The main purpose of this file is to handle sending the password reset link

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $xml = simplexml_load_file('customer.xml');
    if ($xml === false) {
        die("Error loading XML file.");
    }

    $emailExists = false;
    foreach ($xml->customer as $customer) {
        if ((string) $customer->email === $email) {
            $emailExists = true;
            $resetToken = bin2hex(random_bytes(16));
            $customer->addChild('resetToken', $resetToken);
            $xml->asXML('customer.xml');

            // Send email with the reset link
            $resetLink = "http://yourdomain.com/reset_password.php?token=" . $resetToken;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: " . $resetLink;
            $headers = "From: no-reply@yourdomain.com";

            if (mail($email, $subject, $message, $headers)) {
                echo "Password reset link has been sent to your email address.";
            } else {
                echo "Failed to send password reset link.";
            }
            break;
        }
    }

    if (!$emailExists) {
        $_SESSION['error'] = "Email not found.";
        header('Location: forgot_password.php');
        exit();
    }
}
?>
