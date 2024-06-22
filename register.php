<?php
//Student name:Shashank Chauhan 
//The main purpose of this file is to validate login and registration as well as handle placeBid and buyItNow functions used to place bid or buy an item from auction
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Path to the XML file
$customerXMLPath = 'customer.xml'; #change to this later xml/customer.xml

// Handling Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $xml = simplexml_load_file($customerXMLPath);
    if ($xml === false) {
        die("Error loading XML file.");
    }

    $isValidLogin = false;
    $user_customerId=""; 
    foreach ($xml->customer as $customer) {
        if ($customer->email == $email && crypt($password, $customer->password)) {
            $isValidLogin = true;
            $user_customerId = (string) $customer->customerId;
            break;
        }
    }

    if ($isValidLogin) {
        $_SESSION['logged_in'] = true;
        $_SESSION['customerId'] = $user_customerId; // Store the customer ID in session
        header('Location: list.php');
        exit;
    }
     else {
        $_SESSION['error'] = "Email or Password Incorrect";
        header('Location: login.php?error=login'); // Failed login
        exit;
    }
}

// Handling Registration
if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header('Location: login.php?register=1');
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header('location: login.php?register=1');
        exit;
    }    

    $xml = simplexml_load_file($customerXMLPath);
    if ($xml === false) {
        die("Error loading XML file.");
    }

    // Check if email already exists
    $emailExists = false;
    foreach ($xml->customer as $customer) {
        if ($customer->email == $email) {
            $emailExists = true;
            break;
        }
    }

    if ($emailExists) {
        $_SESSION['error'] = "Email already registered.";
        header('Location: login.php?register=1');
        exit;
    }

    // Add new user to XML with hashed password
    $hashedPassword = crypt($password);
    $newCustomer = $xml->addChild('customer');
    $newCustomer->addChild('customerId', count($xml->children()) + 1);  // Assuming ID is a sequence number
    $newCustomer->addChild('firstname', $firstname);
    $newCustomer->addChild('surname', $surname);
    $newCustomer->addChild('email', $email);
    $newCustomer->addChild('password', $hashedPassword);

    $xml->asXML($customerXMLPath);
    // Sending the welcome email
    //$to = $email;
    //$subject = "Welcome to ShopOnline!";
    //$message = "Dear " . $firstname . ", welcome to use ShopOnline! Your customer id is " . (count($xml->children()) + 1) . " and the password is " . $password . ".";
    //$headers = "From: registration@shoponline.com.au";

     //if (!mail($to, $subject, $message, $headers)) {
       // $_SESSION['error'] = "There was an issue sending the welcome email.";
    //}
    echo "Registration successful!";
    header("Location: login.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]))  {
    $action = $_POST["action"];
    if ($action == "placeBid") {
         // Check if the user is logged in.
         if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo "You need to be logged in to place a bid.";
            exit();
        }
        $xml = simplexml_load_file('auction.xml');
        $itemIndex = intval($_POST["itemIndex"]);
        $bidAmount = floatval($_POST["bidAmount"]);
        $bidderId = $_SESSION['customerId'];

        $item = $xml->item[$itemIndex];
        $startPrice = floatval($item->startPrice);
        $latestBidPrice = isset($item->latestBid->bidPrice) ? floatval($item->latestBid->bidPrice) : $startPrice;
        $status = (string)$item->status;

        if ($status != "sold" && $bidAmount > $latestBidPrice) {
            $item->latestBid->bidPrice = $bidAmount;
            $item->latestBid->bidderId = $bidderId;
            $xml->asXML('auction.xml');
            echo "Thank you! Your bid is recorded in ShopOnline.";
        } else {
            echo "Sorry, your bid is not valid or the item is already sold.";
        }
    
    
        
    } elseif ($action == "buyItNow") {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
           echo "You need to be logged in to place a bid.";
           exit();
        }
        $xml = simplexml_load_file('auction.xml');
        $itemIndex = intval($_POST["itemIndex"]);
        $bidderId = $_SESSION['customerId'];
        $xml->item[$itemIndex]->status = "sold";
        $buyNowPrice = (float)$xml->item[$itemIndex]->buyItNowPrice;
        $xml->item[$itemIndex]->latestBid->bidPrice = $buyNowPrice;
        $xml->item[$itemIndex]->latestBid->bidderId = $bidderId;
        
        // Check if there's an error saving the XML
        if (!$xml->asXML('auction.xml')) {
            echo "Failed to save XML.";
            exit();
        }
    
        echo "Thank you for purchasing this item.";
    }
    
}

?>
