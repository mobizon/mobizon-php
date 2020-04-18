<?php

/**
 * This example illustrates how to get full list of your alphanumeric names using Mobizon API.
 *
 * All LIST operations in another API modules have the same behavior and same parameters structure,
 * so You could just use this as starting point to get any list You need.
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

echo "Fetch all user alphanames..." . PHP_EOL;

if ($api->call("alphaname", "list")) {
    if ($api->hasData() && $api->hasData("items")) {
        $data = $api->getData("items");

        echo "Total of {$api->getData('totalItemCount')} items found. Current subset of data:" . PHP_EOL;

        foreach ($data as $row) {
            echo str_pad($row->alphaname->name, 30)
                . "\t" . $row->alphanameId
                . "\t" . $row->globalStatus
                . "\t" . $row->partnerStatus
                . "\t" . $row->description . PHP_EOL;
        }
    } else {
        echo "No alphanames found." . PHP_EOL;
    }
} else {
    var_dump([
        "cod" => $api->getCode(),
        "error" => $api->getMessage()
    ]);
}