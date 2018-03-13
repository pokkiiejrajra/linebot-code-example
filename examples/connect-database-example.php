<?php

require_once('./vendor/autoload.php');

use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;

$channel_token = '6VDxlC/den1sv3g43zYlYQNXm9bTffu3bdcnqE0Lagyw5ZsGTEfh6ylaa0epmhUm+u2nAXlrYtcd7QqKHopBjhd4U+4cNg1eC99iKi87OAEVlLUWGMwqadtTcqpfQll8QHZ7cxk5X6sDir+mEut2VwdB04t89/1O/w1cDnyilFU=';
$channel_secret = '2e77c666b7d87e6bb988c0823576b7a5';

// Get message from Line API
$content = file_get_contents('php://input');
$events = json_decode($content, true);

if (!is_null($events['events'])) {

	// Loop through each event
	foreach ($events['events'] as $event) {
    
        // Line API send a lot of event type, we interested in message only.
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

            // Get replyToken
            $replyToken = $event['replyToken'];

            $host = 'ec2-54-204-45-43.compute-1.amazonaws.com';
            $dbname = 'd179vim1h0c4sd';
            $user = 'zsatyuvhtvpafu';
            $pass = '9769abede7612f615437c2b43aa5b80963b2dc3ed86b779b252b74d034069f12';
            $connection = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass); 
            
            $params = array(
                'log' => $event['message']['text'],
            );

            $statement = $connection->prepare("INSERT INTO logs (log) VALUES (:log)");
            $result = $statement->execute($params);

            if($result){
                $respMessage = 'Log:'.$event['message']['text'].' Success';
            }else{
                $respMessage = 'Log:'.$event['message']['text'].' Fail';
            }
            
            $httpClient = new CurlHTTPClient($channel_token);
            $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));

            $textMessageBuilder = new TextMessageBuilder($respMessage);
            $response = $bot->replyMessage($replyToken, $textMessageBuilder);
 
		}
	}
}

echo "OK";
