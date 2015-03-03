<?php
/**
 * @author teslitsky
 * @date 11.02.2015
 * @time 4:35 PM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';

$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Get user balance...' . PHP_EOL;
if ($api->call('User', 'GetOwnBalance') && $api->hasData('balance')) {
    echo 'Current user balance: ' . $api->getData('currency') . ' ' . $api->getData('balance') . PHP_EOL;
} else {
    echo 'Error occurred while fetching user balance: [' . $api->getCode() . '] ' . $api->getMessage() . PHP_EOL;
}
