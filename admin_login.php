<?php
$correct_password = 'root'; // Change this to your desired admin password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if ($password === $correct_password) {
        echo 'valid';
    } else {
        echo 'invalid';
    }
}
?>
