<?php
session_start();
$correct_password = 'root'; // Change this to your desired admin password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if ($password === $correct_password) {
        $_SESSION['admin_logged_in'] = true;
        echo 'valid';
    } else {
        echo 'invalid';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        echo 'valid';
    } else {
        echo 'invalid';
    }
}
?>
