<?php
/**
 * This example illustrates how to get your current balance amount and currency using Mobizon API.
 *
 * API setup: https://help.mobizon.com/help/sms-api/sms-api
 * API documentation: http://docs.mobizon.com/api/
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';

$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Get user balance...' . PHP_EOL;
if ($api->call('User', 'GetOwnBalance') && $api->hasData('balance')) {
    echo 'Current user balance: ' . $api->getData('currency') . ' ' . $api->getData('balance') . PHP_EOL;
} else {
    echo 'Error occurred while fetching user balance: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
}
