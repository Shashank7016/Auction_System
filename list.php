<?php
//Student name:Shashank Chauhan 
//The main purpose of this file is to take user input for details of item to be placed in the auction
session_start();
$categories = [
    'Electronics' => true,
    'Industrial' => true,
    'Other' => true
];

// Add unique categories from the XML
if (file_exists('auction.xml')) {
    $xml = simplexml_load_file('auction.xml');
    foreach ($xml->item as $item) {
        $category = (string) $item->category;
        if (!isset($categories[$category])) {  // Check if category is not already in the array
            $categories[$category] = true;  // Store categories as keys.
        }
    }
}


?>
<div class="listing-form">
    <h2>List an Item for Selling</h2>
    <form action="listItem.php" method="post">
        <label for="itemName">Item Name:</label>
        <input type="text" id="itemName" name="itemName" required><br><br>

        <label for="category">Category:</label>
    <select id="category" name="category">
        <!-- Categories will be populated here -->
        <?php foreach (array_keys($categories) as $category) : ?>
        <option value="<?= $category; ?>"><?= $category; ?></option>
        <?php endforeach; ?>
    </select><br><br>


        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="reservePrice">Reserve Price:</label>
        <input type="number" id="reservePrice" name="reservePrice" step="0.01" required><br><br>

        <label for="buyItNowPrice">Buy It Now Price:</label>
        <input type="number" id="buyItNowPrice" name="buyItNowPrice" step="0.01" required><br><br>

        <label for="startPrice">Start Price (default is 0):</label>
        <input type="number" id="startPrice" name="startPrice" step="0.01" value="0" required><br><br>

        <label for="duration">Duration (in hours):</label>
        <input type="number" id="duration" name="duration" required><br><br>

        <input type="submit" value="List Item">
    </form>
</div>
