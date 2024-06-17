<?php
//Student name:Shashank Chauhan 
//The main purpose of this file is to send data to be stored in an xml so that they can be placed for bidding

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extracting form values
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
        $sellerId = $_SESSION['customerId'];
    } else {
        $sellerId = ""; // or some default value
    }
    $itemName = $_POST["itemName"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $reservePrice = $_POST["reservePrice"];
    $buyItNowPrice = $_POST["buyItNowPrice"];
    $startPrice = $_POST["startPrice"];
    $duration = $_POST["duration"];

    // Validation
    if ($startPrice > $reservePrice) {
        die("Start price must be no more than the reserve price.");
    }
    if ($reservePrice >= $buyItNowPrice) {
        die("Reserve price must be less than the buy-it-now price.");
    }

    // Load auction.xml or create a new one
    if (file_exists('auction.xml')) {
        $xml = simplexml_load_file('auction.xml');
    } else {
        $xml = new SimpleXMLElement('<items></items>');
    }

    // Generate a unique item number
    $itemNumber = (count($xml->children()) + 1);

    // Add new item to auction.xml
    $item = $xml->addChild('item');
    $item->addChild('itemNumber', $itemNumber);
    $item->addChild('sellerId', $sellerId);

    $item->addChild('itemName', $itemName);
    $item->addChild('category', $category);
    $item->addChild('description', $description);
    $item->addChild('reservePrice', $reservePrice);
    $item->addChild('buyItNowPrice', $buyItNowPrice);
    $item->addChild('startPrice', $startPrice);
    $item->addChild('duration', $duration);
    $item->addChild('startDate', date("Y-m-d"));
    $item->addChild('startTime', date("H:i:s"));
    $item->addChild('status', 'in_progress');
    $item->addChild('latestBid')->addChild('bidderID', '');
    $item->latestBid->addChild('bidPrice', $startPrice);
    
    // Save changes to auction.xml
    $xml->asXML('auction.xml');

    echo "Thank you! Your item has been listed in ShopOnline. The item number is " . $itemNumber . ", and the bidding starts now: " . date("H:i:s") . " on " . date("Y-m-d") . ".";
}
?>
