<?php

/**
 * This example illustrates how to send single SMS message using Mobizon API.
 *
 * API documentation: https://help.mobizon.com/help/api-docs
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';

use Mobizon\MobizonApi;

$api = new MobizonApi(
    array(
        "apiKey" => "YOUR_API_KEY",
        "apiServer" => "api.mobizon.gmbh", // [ api.mobizon.gmbh, api.mobizon.kz, api.mobizon.com ]
        "forceHTTP" => true
    )
);

echo "Send message..." . PHP_EOL;

$alphaname = 0; // Optional, if you don"t have registered alphaname, just skip this param and your message will be sent with our free common alphaname.

$recipient = [
    "recipient" => "11999000000", // [DDD + NUMBER Ex: 11999000000]
    "text" => "TEST SMS API MOBIZON",
    "from" => $alphaname,
];

if ($api->call(
    "message",
    "sendSMSMessage",
    $recipient
)) {
    $messageId = $api->getData("messageId");

    echo "Message created with ID:{$messageId}" . PHP_EOL;

    if ($messageId) {
        echo "Get message info..." . PHP_EOL;

        $messageStatuses = $api->call(
            "message",
            "getSMSStatus",
            array(
                "ids" => array($messageId, "13394", "11345", "4393")
            ),
            array(),
            true
        );

        if ($api->hasData()) {
            foreach ($api->getData() as $messageInfo) {
                var_dump([
                    "cod" => $messageInfo->id,
                    "message" => $recipient['text'],
                    "status" => $messageInfo->status
                ]);
            }
        }
    }
} else {
    var_dump([
        "cod" => $api->getCode(),
        "error" => $api->getMessage()
    ]);
}