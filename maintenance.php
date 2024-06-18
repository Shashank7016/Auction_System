<?php
//The main purpose of this file is to validate logic for processing and generating reports
header('Content-Type: application/json');

if (isset($_POST['action'])) {
    $xml = simplexml_load_file('auction.xml');
    $currentDateTime = new DateTime();
    $totalRevenueFile = 'total_revenue.txt';
    $totalRevenue = 0;

    // Read the existing total revenue
    if (file_exists($totalRevenueFile)) {
        $totalRevenue = (float)file_get_contents($totalRevenueFile);
    }

    if ($_POST['action'] == 'processAuctionItems') {
        $soldItemsCount = 0;
        $failedItemsCount = 0;

        foreach ($xml->item as $item) {
            $startDate = new DateTime((string)$item->startDate);
            $startTime = new DateTime((string)$item->startTime);
            $duration = new DateInterval('PT' . $item->duration . 'S');
            $endDateTime = clone $startDate;
            $endDateTime->add($duration);

            $status = (string)$item->status;
            $bidPrice = isset($item->latestBid->bidPrice) ? (float)$item->latestBid->bidPrice : 0;
            $reservePrice = (float)$item->reservePrice;
            $buyItNowPrice = (float)$item->buyItNowPrice;

            if (($currentDateTime >= $endDateTime || $_POST['action'] == 'processAuctionItems') && $status == "in_progress") {
                if ($bidPrice >= $buyItNowPrice && $buyItNowPrice != 0) {
                    $item->status = "sold";
                    $totalRevenue += 0.03 * $buyItNowPrice;
                    $soldItemsCount++;
                }
                if ($bidPrice >= $reservePrice && $bidPrice < $buyItNowPrice) {
                    $item->status = "sold";
                    $totalRevenue += 0.01 * $bidPrice;
                    $soldItemsCount++;
                }
                if ($bidPrice < $reservePrice) {
                    $item->status = "failed";
                    $failedItemsCount++;
                }
            }
            elseif (($currentDateTime >= $endDateTime || $_POST['action'] == 'processAuctionItems') && $status == "sold") {
                if ($bidPrice == $buyItNowPrice && $buyItNowPrice != 0) {
                    $totalRevenue += 0.03 * $buyItNowPrice;
                    $item->buyItNowPrice = 0; // Set buyItNowPrice to 0 to prevent re-processing
                    $soldItemsCount++;
                }
            }
        }

        // Save the updated XML structure
        $xml->asXML('auction.xml');

        // Save the updated total revenue
        file_put_contents($totalRevenueFile, $totalRevenue);

        echo json_encode([
            'message' => 'Items processed successfully.',
            'soldItems' => $soldItemsCount,
            'failedItems' => $failedItemsCount
        ]);
    }

    elseif ($_POST['action'] == 'generateReport') {
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
            $bidPrice = isset($item->latestBid->bidPrice) ? (float)$item->latestBid->bidPrice : 0;
            $reservePrice = (float)$item->reservePrice;
            $buyItNowPrice = (float)$item->buyItNowPrice;
            $revenue = 0;

            if ($status == "sold") {
                if ($bidPrice >= $buyItNowPrice) {
                    $revenue = 0.03 * $bidPrice;
                } else {
                    $revenue = 0.01 * $bidPrice;
                }
            }

            $output .= "<tr>
                            <td>{$item->itemNumber}</td>
                            <td>{$item->itemName}</td>
                            <td>{$status}</td>
                            <td>" . ($status == "sold" ? $bidPrice : $reservePrice) . "</td>
                            <td>{$revenue}</td>
                        </tr>";
        }

        $output .= "</table><br>";
        $output .= "Total Revenue: " . $totalRevenue;

        echo $output;
        exit();
    }
}
?>
