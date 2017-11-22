<?php
/**
 * This example illustrates how to send single SMS message using Mobizon API.
 *
 * API setup: https://help.mobizon.com/help/sms-api/sms-api
 * API documentation: http://docs.mobizon.com/api/
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';

$api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

echo 'Send message...' . PHP_EOL;
$alphaname = 'TEST';
if ($api->call('message',
    'sendSMSMessage',
    array(
        'recipient' => '77770000000',
        'text'      => 'Test sms message',
        'from'      => $alphaname, //Optional, if you don't have registered alphaname, just skip this param and your message will be sent with our free common alphaname.
    ))
) {
    $messageId = $api->getData('messageId');
    echo 'Message created with ID:' . $messageId . PHP_EOL;

    if ($messageId) {
        echo 'Get message info...' . PHP_EOL;
        $messageStatuses = $api->call(
            'message',
            'getSMSStatus',
            array(
                'ids' => array($messageId, '13394', '11345', '4393')
            ),
            array(),
            true
        );

        if ($api->hasData()) {
            foreach ($api->getData() as $messageInfo) {
                echo 'Message # ' . $messageInfo->id . " status:\t" . $messageInfo->status . PHP_EOL;
            }
        }
    }
} else {
    echo 'An error occurred while sending message: [' . $api->getCode() . '] ' . $api->getMessage() . 'See details below:' . PHP_EOL;
    var_dump(array($api->getCode(), $api->getData(), $api->getMessage()));
}
