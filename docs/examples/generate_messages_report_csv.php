<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';
$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Get the user SMS campaign for the period' . PHP_EOL;

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
$provider = 'campaign';

//Method
$method = 'list';

//Data element
$dataElement = 'items';

//Total items counter
$totalItemCountElement = 'totalItemCount';
//Sets++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Create (or replace) file with header in first line - be careful, if you have file with such name, it will be overwritten
$headerString = 'Message ID;Campaign ID;Date Created;Date Sent;Message Status;Segments;Segment Price;Message Price;Alphaname;Phone Number;Message Text';
file_put_contents($saveDir . $saveFileName, $headerString);

//TODO: это должно быть в цикле, чтобы извлечь все кампании, если их больше 25
if ($api->call($provider, $method, array('criteria[createDateFrom]' => $dateBegin, 'criteria[createDateTo]' => $dateEnd, "sort[{$fieldSort}]" => $typeSort)))
{
    if ($api->hasData($totalItemCountElement) && $api->hasData($dataElement))
    {
        $campaignsData = $api->getData($dataElement);

        echo 'Total of ' . $api->getData($totalItemCountElement) . ' items found. Current subset of data:' . PHP_EOL;

        foreach ($campaignsData as $campaignRow)
        {
            //TODO: это должно быть в цикле, чтобы извлечь все сообщения кампании, если их больше 25
            if ($api->call('message', 'list', array('criteria[campaignId]' => $campaignRow->id)))
            {
                if ($api->hasData($totalItemCountElement) && $api->hasData($dataElement))
                {
                    $messagesData = $api->getData($dataElement);
                    foreach ($messagesData as $messageRow)
                    {
                        print_r($messageRow);
                        $formattedRow = $messageRow->id
                        . ";" . $campaignRow->id
                        . ";" . $campaignRow->createTs
                        . ";" . $campaignRow->createTs
                        . ";" . $messageRow->status
                        . ";" . $messageRow->segNum
                        . ";" . $messageRow->segUserBuy
                        . ";" . $messageRow->segNum*$messageRow->segUserBuy
                        . ";" . empty($messageRow->from) ? $campaignRow->from : $messageRow->from
                        . ";+" . $messageRow->to
                        . ";" . '"' . empty($messageRow->text) ? $campaignRow->text : $messageRow->text . '"';
                        file_put_contents($saveDir . $saveFileName, PHP_EOL . $formattedRow, FILE_APPEND);
                    }
                }
            }
        }
    }
    else
    {
        echo 'No compaigns found.' . PHP_EOL;
    }
}
else
{
    echo 'Error occurred while fetching sms campaigns list: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
}

?>