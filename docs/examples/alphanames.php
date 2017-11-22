<?php
/**
 * This example illustrates how to get full list of your alphanumeric names using Mobizon API.
 *
 * All LIST operations in another API modules have the same behavior and same parameters structure,
 * so You could just use this as starting point to get any list You need.
 *
 * API setup: https://help.mobizon.com/help/sms-api/sms-api
 * API documentation: http://docs.mobizon.com/api/
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';

$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Fetch all user alphanames...' . PHP_EOL;
if ($api->call('alphaname', 'list')) {
    if ($api->hasData() && $api->hasData('items')) {
        $data = $api->getData('items');
        echo 'Total of ' . $api->getData('totalItemCount') . ' items found. Current subset of data:' . PHP_EOL;
        foreach ($data as $row) {
            echo str_pad($row->alphaname->name, 30)
                . "\t" . $row->alphanameId
                . "\t" . $row->globalStatus
                . "\t" . $row->partnerStatus
                . "\t" . $row->description . PHP_EOL;
        }
    } else {
        echo 'No alphanames found.' . PHP_EOL;
    }
} else {
    echo 'Error occurred while fetching alphanames list: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
}
