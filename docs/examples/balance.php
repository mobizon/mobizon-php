<?php

/**
 * This example illustrates how to get your current balance amount and currency using Mobizon API.
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

echo "Get user balance..." . PHP_EOL;

if ($api->call("User", "GetOwnBalance") && $api->hasData("balance")) {
    var_dump([
        'currency' => $api->getData("currency"),
        'balance' => $api->getData("balance")
    ]);
} else {
    var_dump([
        'cod' => $api->getCode(),
        'error' => $api->getMessage()
    ]);
}