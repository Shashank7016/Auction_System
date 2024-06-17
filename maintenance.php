<?php
//The main purpose of this file is to validate logic for processing and generating reports
header('Content-Type: application/json');

if (isset($_POST['action'])) {
    $xml = simplexml_load_file('auction.xml');
    if ($_POST['action'] == 'processAuctionItems') {
        $currentDateTime = new DateTime();
        $soldItemsCount = 0;
        $failedItemsCount = 0;
    
        // Create a new XML structure for items
        $newXML = new SimpleXMLElement("<auctions></auctions>");
    
        foreach ($xml->item as $item) {
            $startDate = new DateTime($item->startDate);
            $startTime = new DateTime($item->startTime);
            $duration = new DateInterval('P' . $item->duration . 'D');
            $endDate = $startDate->add($duration);
            $endTime = $startTime->add($duration);
    
            $itemEndDateTime = new DateTime($endDate->format('Y-m-d') . ' ' . $endTime->format('H:i:s'));
    
            // Check if item is expired
            if ($itemEndDateTime <= $currentDateTime) {
                // Update status to "sold" if bid price >= reserve price
                if (floatval($item->latestBid->bidPrice) >= floatval($item->reservePrice)) {
                    $item->status = "sold";
                    $soldItemsCount++;
                } else {
                    $item->status = "failed";
                    $failedItemsCount++;
                }
            } else {
                // Add this item to newXML
                $newItem = $newXML->addChild("item");
                foreach ($item->children() as $child) {
                    $newItem->addChild($child->getName(), $child);
                }
            }
        }
    
        // Save the new XML structure
        $newXML->asXML('auction.xml');
    
        echo json_encode([
            'message' => 'Items processed successfully.',
            'soldItems' => $soldItemsCount,
            'failedItems' => $failedItemsCount
        ]);
    }
    
    elseif ($_POST['action'] == 'generateReport') {
        $xml = simplexml_load_file('auction.xml');
    
        $totalRevenue = 0;
        $output = "<table border='1'>
                    <tr>
                        <th>Item Number</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Sold Price/Reserved Price</th>
                        <th>Revenue</th>
                    </tr>";
    
        foreach ($xml->item as $item) {
            $status = (string)$item->status;
            if ($status == "sold" || $status == "failed") {
                $price = (float)$item->latestBid->bidPrice;
                $reservePrice = (float)$item->reservePrice;
                $revenue = 0;
                if ($status == "sold") {
                    $revenue = 0.03 * $price;
                } else {
                    $revenue = 0.01 * $reservePrice;
                }
                $totalRevenue += $revenue;
                $output .= "<tr>
                            <td>{$item->itemNumber}</td>
                            <td>{$item->itemName}</td>
                            <td>{$status}</td>
                            <td>" . ($status == "sold" ? $price : $reservePrice) . "</td>
                            <td>{$revenue}</td>
                        </tr>";
            }
        }
    
        $output .= "</table><br>";
        $output .= "Total Revenue: " . $totalRevenue;
    
        echo $output;
        exit();
    }
}
?>
