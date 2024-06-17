<?php
// Student name: Shashank Chauhan 
// The main purpose of this file is to check if a user is logged into the system or not

session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging purposes (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = array("loggedIn" => false);

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $response["loggedIn"] = true;
}

echo json_encode($response);
exit();
?>
