<?php
/**
 * This example illustrates how to get SMS messages report using Mobizon API and create CSV file from data received.
 *
 * API setup: https://help.mobizon.com/help/sms-api/sms-api
 * API documentation: http://docs.mobizon.com/api/
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';
$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Get the user SMS messages for the period' . PHP_EOL;

//Sets++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Dates Begin and End
$dateBegin = date('Y-m-01');
$dateEnd = date('Y-m-d');

//Directory to save csv data
$saveDir = '.' . DIRECTORY_SEPARATOR;

//File name to save CSV data
$saveFileName = 'messages_report_' . $dateBegin . '_' . $dateEnd . '.csv';

//Sort data type
$fieldSort = 'id';
$typeSort = 'asc';

//Provider
$provider = 'message';

//Method
$method = 'list';

//Data element
$dataElement = 'items';

//Total items counter
$totalItemCountElement = 'totalItemCount';
//Sets++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Create (or replace) file with header in first line - be careful, if you have file with such name, it will be overwritten
$headerString = 'Message ID;Campaign ID;Date Sending Started;Date Status Updated;Message Status;Segments;Segment Price;Message Price;Alphaname;Phone Number;Message Text';
file_put_contents($saveDir . $saveFileName, $headerString);

$page = 0;
$pageSize = 100;
$total = null;
//cycle through pages to extract all results, not only the first page
do {
    if ($total !== null && $pageSize * $page >= $total) {
        break;
    }
    if ($api->call(
        $provider,
        $method,
        array(
            'criteria[campaignCreateDateFrom]' => $dateBegin,
            'criteria[campaignCreateDateTo]' => $dateEnd,
            "sort[{$fieldSort}]" => $typeSort,
            'pagination[pageSize]' => $pageSize,
            'pagination[currentPage]' => $page
        ))
    ) {
        if ($api->hasData($totalItemCountElement) && $api->hasData($dataElement)) {
            $total = (int)$api->getData($totalItemCountElement);
            $campaignsData = $api->getData($dataElement);

            echo 'Total of ' . $total . ' items found. Current subset of data from ' . $page * $pageSize . ' to ' . min($total - 1,
                    ($page * $pageSize - 1)) . ':' . PHP_EOL;

            $messagesData = $api->getData($dataElement);
            foreach ($messagesData as $messageRow) {
                $formattedRow = $messageRow->id
                . ";" . $messageRow->campaignId
                . ";" . $messageRow->startSendTs
                . ";" . $messageRow->statusUpdateTs
                . ";" . $messageRow->status
                . ";" . $messageRow->segNum
                . ";" . $messageRow->segUserBuy
                . ";" . $messageRow->segNum * $messageRow->segUserBuy
                . ";" . $messageRow->from
                    . ";+" . $messageRow->to
                    . ";" . '"' . str_replace("\n", '', $messageRow->text) . '"';
                file_put_contents($saveDir . $saveFileName, PHP_EOL . $formattedRow, FILE_APPEND);
            }
            $page++;
        } else {
            echo 'No messages found. Exit loop.' . PHP_EOL;
            break;
        }
    } else {
        echo 'Error occurred while fetching sms messages list: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
    }
} while (1);
