<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');
define('CONFIRMATION_STRING',' 0571d59d');
define('RESPONSE_SUCCESS','ok');
define('GROUP_TOKEN','19836e2c377bd86f0b11ae2abfdfaa9506d2b756dd817fa80e480d3a6e9829a13445118c512141ff1a6b8');
define('MESSAGE_SEND','https://api.vk.com/method/messages.send?');
define('VERSION',5.95);

if(!isset($_REQUEST)):
    exit();
endif;

$event = json_decode(file_get_contents('php://input'));
switch ($event->type):
    case CALLBACK_API_EVENT_CONFIRMATION:
        callbackResponse(CONFIRMATION_STRING);
        break;
    case CALLBACK_API_EVENT_MESSAGE_NEW:
        dispatchMsg($event);
        callbackResponse(RESPONSE_SUCCESS);
        break;
endswitch;

function callbackResponse($str){
    echo $str;
    return;
}
   
function dispatchMsg($event){
    $text = $event->object->body;
    $city = mb_strtoupper($text,'UTF-8');
    $sql = 'SELECT title FROM sputnik8 WHERE city_rus =? AND id NOT IN(SELECT id FROM not_available);';
    $data = array($city);
    $excursions = getCityArray($sql, $data);
    $message = count($excursions);
    //foreach($excursions as $title):
    //    $message = $message.(string)$title."\n";
    //endforeach;
    if(count($excursions)==0):
        $message = $city;
    endif;
    messageSendWK($event->object->user_id, $message);
}

function messageSendWK($uid,$messsage){
    $rid = rand(1, (int)pow(10,32));
    $param = array(
        'user_id' =>$uid,
        'access_token' => GROUP_TOKEN,
        'v'=> VERSION,
        'random_id' =>$rid,
        'message' => $messsage
    );
    return file_get_contents(MESSAGE_SEND.http_build_query($param));
}

function getCityArray($sql,$data){
    $excursions =array();
    $host = '127.0.0.1';
    $db   = 'vkassist';
    $user = 'db';
    $pass = 'O7vINQpyW07ctxcd';
    $charset = 'utf8';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);
    $stm = $pdo->prepare($sql);
    $stm->execute($data);
    while($row = $stm->fetch()):
        array_push($excursions,$row->title);
    endwhile;
    return $excursions;
}