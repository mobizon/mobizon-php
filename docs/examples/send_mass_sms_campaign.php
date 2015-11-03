<?php
/**
 * @author i.shcherbak
 * @date 03.11.2015
 * @time 9:59 AM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MobizonApi.php';

try
{
    $api = new Mobizon\MobizonApi('KKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

    $alphaname = 'Mobizon';
    $smsText = 'Test SMS message text!';

    echo 'Create massive campaign...' . PHP_EOL;
    if (!$api->call('campaign', 'create',
        array(
            'data' => array(
                'text'    => $smsText,
                'from'    => $alphaname,
                'msgType' => 'SMS'
            )
        )
    )
    )
    {
        echo 'Campaign not created, unhandled errors.' . PHP_EOL;
        die(__LINE__);
    }

    if (0 == $api->getCode() && $api->hasData())
    {
        $campaignId = $api->getData();
        echo 'Campaign created with ID: ' . $campaignId . PHP_EOL;
    }
    else
    {
        echo 'Campaign not created, errors: ' . print_r($api->getData(), true) . PHP_EOL;
        die(__LINE__);
    }

    echo 'Add recipients...' . PHP_EOL;
    // add 5000 recipients to campaign - only demonstration, don't do this in your real scripts
    // as it will make your account blocked for such activity with sms campaigns!
    // USE ONLY REAL NUMBERS FROM YOUR STORAGE - database, file, service, api, etc
    $counter = 0;
    $total = 5000;
    $start = 77010000000;
    // max count of recipients per request is 500,
    // you should select by 500 numbers from your storage, not all in single request
    // we do 5000 numbers upload in this example, JUST FOR TEST!!!
    // we generate numbers just FOR TEST, DO NOT DO THIS IN REAL SOFTWARE!!!
    $firstCall = true;
    while ($counter < $total)
    {
        $recipientsList = array();
        for ($i = $start + $counter; $i < $start + $counter + min(500, $total - $counter); $i++)
        {
            $recipientsList[] = $i;
        }
        $counter += 500;

        if (!$api->call('campaign',
            'addrecipients',
            array(
                'id'         => $campaignId,
                'recipients' => $recipientsList,
                // actually this could be 0 every new request,
                // but to make sure we call with 1 first time (clears previously added recipients if any)
                'replace'    => $firstCall ? '1' : '0'
            ))
        )
        {
            echo 'An error occurred while adding recipients: [' . $api->getCode() . '] ' . $api->getMessage() . ' See details below:' . PHP_EOL;
            var_dump(array($api->getCode(), $api->getData(), $api->getMessage()));
            die(__LINE__);
        }

        if (0 == $api->getCode() && $api->hasData())
        {
            echo 'Recipients portion added successfully.' . PHP_EOL;
//            echo 'Code: ' . print_r($api->getCode(), true) . PHP_EOL;
//            echo 'Data: ' . print_r($api->getData(), true) . PHP_EOL;
        }
        else
        {
            echo 'An error occurred while adding recipients: [' . $api->getCode() . '] ' . $api->getMessage() . ' See details below:' . PHP_EOL;
            var_dump($api->getData());
            die(__LINE__);
        }
        $firstCall = false;
    }

    //send campaign
    echo 'Confirm campaign send...' . PHP_EOL;
    if (!$api->call('campaign',
        'send',
        array(
            'id' => $campaignId
        ))
    )
    {
        echo 'An error occurred while confirming campaign send: [' . $api->getCode() . '] ' . $api->getMessage() . ' See details below:' . PHP_EOL;
        var_dump($api->getData());
        die(__LINE__);
    }
    echo 'Campaign #' . $campaignId . ' has been sent.' . PHP_EOL;
}
catch (\Exception $e)
{
    echo 'An error occured in communication process: ' . $e->getMessage() . PHP_EOL;
    die(__LINE__);
}