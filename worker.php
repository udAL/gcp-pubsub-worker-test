<?php
require_once './includes/init.php';

/**
 * Funcio per generar i comprovar la subscripcio a PubSub
 */
use Google\Cloud\PubSub\PubSubClient;
$keyFile = 'pubsub_secret.json' ;
$pubSub = null;
$subscription = null;
$pubSub = new PubSubClient([
    'keyFilePath' => 'includes/' . $keyFile
]);
$subscription = $pubSub->subscription('workers_test');
if(!$subscription->exists())
{
    trigger_error('Missing subscription "workers_test"', E_USER_ERROR);
    die;
}

while(true)
{
    $message = false;
    foreach ($subscription->pull(['maxMessages' => 1]) as $pullMessage)
    {
        $message = $pullMessage;
    }
    if ($message)
    {
        sleep(10);
        $subscription->acknowledge($message);
    }
}