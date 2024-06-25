<?php
// Student name: Shashank Chauhan
// The main purpose of this file is to handle user logout
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
