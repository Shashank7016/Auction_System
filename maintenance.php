<?php
//The main purpose of this file is to validate logic for processing and generating reports
header('Content-Type: application/json');

if (isset($_POST['action'])) {
    $xml = simplexml_load_file('auction.xml');
    $currentDateTime = new DateTime();

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
            $startPrice = (float)$item->startPrice;

            // Check if item duration has ended
            if ($currentDateTime >= $endDateTime) {
                if ($status == "in_progress" && $bidPrice >= $reservePrice) {
                    $item->status = "sold";
                    $soldItemsCount++;
                } else if ($status == "in_progress" && $bidPrice < $reservePrice && $bidPrice > 0) {
                    $item->status = "sold";
                    $soldItemsCount++;
                } else if ($status == "in_progress" && $bidPrice == 0) {
                    $item->status = "failed";
                    $failedItemsCount++;
                }
            } else if ($status == "sold") {
                $soldItemsCount++;
            } else if ($status == "in_progress" && $bidPrice == 0) {
                // No bid made, item still in progress, do not count yet
                continue;
            }
        }

        // Save the updated XML structure
        $xml->asXML('auction.xml');

        echo json_encode([
            'message' => 'Items processed successfully.',
            'soldItems' => $soldItemsCount,
            'failedItems' => $failedItemsCount
        ]);
    }

    elseif ($_POST['action'] == 'generateReport') {
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
            $bidPrice = isset($item->latestBid->bidPrice) ? (float)$item->latestBid->bidPrice : 0;
            $reservePrice = (float)$item->reservePrice;
            $buyItNowPrice = (float)$item->buyItNowPrice;
            $startPrice = (float)$item->startPrice;
            $price = $status == "sold" ? $bidPrice : ($status == "in_progress" ? $bidPrice : $startPrice);
            $revenue = 0;

            if ($status == "sold") {
                $revenue = 0.03 * ($buyItNowPrice > 0 ? $buyItNowPrice : $bidPrice);
            } elseif ($status == "in_progress") {
                $revenue = 0.01 * ($bidPrice > 0 ? $bidPrice : $startPrice);
            }

            $totalRevenue += $revenue;

            $output .= "<tr>
                            <td>{$item->itemNumber}</td>
                            <td>{$item->itemName}</td>
                            <td>{$status}</td>
                            <td>" . ($status == "sold" ? ($buyItNowPrice > 0 ? $buyItNowPrice : $bidPrice) : $startPrice) . "</td>
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
