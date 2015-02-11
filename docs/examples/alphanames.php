<?php
/**
 * @author teslitsky
 * @date 11.02.2015
 * @time 4:37 PM
 */

require_once __DIR__ . '/../../src/MobizonApi.php';

$apiKey = '65108e474c6a0af679629d9559609ca81cd3f929';
$api = new Mobizon\MobizonApi($apiKey);

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
