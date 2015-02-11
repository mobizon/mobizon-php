<?php
/**
 * @author teslitsky
 * @date 11.02.2015
 * @time 4:35 PM
 */

require_once __DIR__ . '/../../src/MobizonApi.php';

$apiKey = '65108e474c6a0af679629d9559609ca81cd3f929';
$api = new Mobizon\MobizonApi($apiKey);

echo 'Get user balance...' . PHP_EOL;
if ($api->call('User', 'GetOwnBalance') && $api->hasData('balance')) {
    echo 'Current user balance: ' . $api->getData('currency') . ' ' . $api->getData('balance') . PHP_EOL;
} else {
    echo 'Error occurred while fetching user balance: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
}
