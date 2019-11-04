<?php
require_once './includes/init.php';

// -- Connect to PubSub --
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

// -- Main loop --
$task_counter = 1;
echo "Ready" . PHP_EOL;
echo "Waiting for tasks..." . PHP_EOL;
while(true)
{
    // -- Waiting for message --
    $message = false;
    foreach ($subscription->pull(['maxMessages' => 1]) as $pullMessage)
    {
        $message = $pullMessage;
    }

    // -- Message received --
    if ($message)
    {
        echo "New task #" . $task_counter . PHP_EOL;
        sleep(10);
        $subscription->acknowledge($message);
        echo "Finished #" . $task_counter . PHP_EOL;
        $task_counter++;
    }
}