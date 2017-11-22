<?php
/**
 * This example illustrates how to get SMS campaigns report using Mobizon API and create CSV file from data received.
 *
 * API setup: https://help.mobizon.com/help/sms-api/sms-api
 * API documentation: http://docs.mobizon.com/api/
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';
$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Get the user SMS campaigns list for the period.' . PHP_EOL;

//Sets++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Dates Begin and End
$dateBegin = date('Y-m-01');
$dateEnd = date('Y-m-d');

//Directory to save csv data
$saveDir = '.' . DIRECTORY_SEPARATOR;

//File name to save CSV data
$saveFileName = 'campaigns_report_' . $dateBegin . '_' . $dateEnd . '.csv';

//Sort data type
$fieldSort = 'id';
$typeSort = 'asc';

//Provider
$provider = 'campaign';

//Method
$method = 'list';

//Data element
$dataElement = 'items';

//Total items counter
$totalItemCountElement = 'totalItemCount';
//Sets++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Create (or replace) file with header in first line - be careful, if you have file with such name, it will be overwritten
$headerString = 'Campaign ID;Date Created;Date Sending Started;Date Sending Finished;Campaign Status;Messages;Segments;Total Price;Segments Delivered;From;Message Text';
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
            'criteria[createDateFrom]' => $dateBegin,
            'criteria[createDateTo]' => $dateEnd,
            "sort[{$fieldSort}]" => $typeSort,
            'pagination[pageSize]' => $pageSize,
            'pagination[currentPage]' => $page
        ))
    ) {
        if ($api->hasData($totalItemCountElement) && $api->hasData($dataElement)) {
            $total = (int)$api->getData($totalItemCountElement);
            $campaignsData = $api->getData($dataElement);

            echo 'Total of ' . $api->getData($totalItemCountElement) . ' campaigns found.' . PHP_EOL;
            foreach ($campaignsData as $campaignRow) {
                $formattedRow = $campaignRow->id
                    . ";" . $campaignRow->createTs
                    . ";" . (empty($campaignRow->startSendTs) ? '-' : $campaignRow->startSendTs)
                    . ";" . (empty($campaignRow->endSendTs) ? '-' : $campaignRow->endSendTs)
                    . ";" . $campaignRow->commonStatus
                    . ";" . $campaignRow->counters->totalMsgNum
                    . ";" . $campaignRow->counters->totalMsgNum
                    . ";" . $campaignRow->counters->totalCost
                    . ";" . $campaignRow->counters->totalDelivrdSegNum
                    . ";" . $campaignRow->from
                    . ";" . '"' . str_replace("\n", '', $campaignRow->text) . '"';
                file_put_contents($saveDir . $saveFileName, PHP_EOL . $formattedRow, FILE_APPEND);
            }
        } else {
            echo 'No compaigns found.' . PHP_EOL;
        }
    } else {
        echo 'Error occurred while fetching sms campaigns list: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
    }

} while (1);
