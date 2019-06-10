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
define('GET_HISTORY','https://api.vk.com/method/messages.getHistory?');
define('VERSION',5.95);
require_once 'keyboard.php';

if(!isset($_REQUEST)):
    exit();
endif;

$event = json_decode(file_get_contents('php://input'));
switch ($event->type):
    case CALLBACK_API_EVENT_CONFIRMATION:
        callbackResponse(CONFIRMATION_STRING);
        break;
    case CALLBACK_API_EVENT_MESSAGE_NEW:
        callbackResponse(RESPONSE_SUCCESS);
        dispatchMsg($event);
        break;
endswitch;

function callbackResponse($str){
    echo $str;
    return;
}
   
function dispatchMsg($event){
    if(empty($event->object->payload)):
        $button = 'No button';
        messageSendWOK($event->object->from_id, $button);
        showCountriesScreen($event->object->from_id);
        return;
    else:
        $button = json_decode($event->object->payload)->button;
    endif;
    if(intdiv($button,3000)):
        excursionsProcess($button,$event);
    elseif(intdiv($button,2000)):
        citiesProcess($button,$event);
    elseif(intdiv($button,1000)):
        countriesProcess($button, $event);
    endif;
    return;
}

function citiesProcess($button, $event){
  //  $history = messagesGetHistory($event->object->from_id);
    $country = getCoutnryFromScreen($event);
    $screen = $button - 2990;
    if($screen > 0):
        showCitiesScreen($event,$country, $screen,'Select city');
    elseif(!$screen):
        showCountriesScreen($event->object->from_id);
    else:        
        showTitlesScreen($event->object->from_id,$event->object->text);
    endif;
    return;
}

function getCoutnryFromScreen($event){
    $eu = getCountries();
    foreach(messagesGetHistory($event->object->from_id) as $message):
        if (in_array($message->text,$eu)):
            $country = $message->text;
            break;
        endif;
    endforeach;
    if(empty($country)):
        return false;
    else:
        return $country;
    endif;
}


function getCityFromScreen($event, $country){
    $country_id = getCountryID($country);
    $cities = getCities($country_id);
    foreach(messagesGetHistory($event->object->from_id) as $message):
        if (in_array($message->text,$cities)):
            $city = $message->text;
            break;
        endif;
    endforeach;
    if(empty($city)):
        return false;
    else:
        return $city;
    endif;
}


function excursionsProcess($button,$event){
    $country = getCoutnryFromScreen($event);
    $city = getCityFromScreen($event, $country);
    $screen = $button - 3990;
    if($screen > 0):
        showTitlesScreen($event->object->from_id,$city, $screen,'Select excursion\'s number');
    elseif(!$screen):
        showCitiesScreen($event,$country, 1 ,'Select city');
    else:        
        showTitlesScreen($event->object->from_id,$city,1,'Select excursion\'s number');
    endif;
    return;
    
}



function showTitlesScreen($uid, $city,$screen=1){
    $labels = array();
    $titleIDs = array();
    $offset = 3000;
    $city_id = getCityID($city);
    $titles = getTitles($city_id);
    for($j = 0; $j < count($titles); $j++):
        array_push($labels,(string)$j+1);
        array_push($titleIDs, $j+1);
    endfor;
    $titleK = new Keyboard();
    $titleK->setParam(getParam($screen,$offset,$labels,$titleIDs));
    $keyboard = $titleK->getKeyboard();
    messageSendWK($uid,getTitlesList($titles,$screen), $keyboard);
}



function getTitlesList($titles,$screen){
    $message ='';
    if(empty($titles[0])):
        return 'No excursions';    
    endif;
    $k = ($screen -1)*7;
    $n = 1;
    $titlesArray = array_slice($titles, $k, 7);
    foreach($titlesArray as $title):
        $message = $message.($k+$n).". ".$title."\n";
        $n++;
    endforeach;
    return $message;
}


function countriesProcess($button, $event){
    $proc = $button - 1990;
    if($proc >= 0):
        $screen = $proc;
        if(!$proc):
            $screen = 1;
        endif;
        showCountriesScreen($event->object->from_id, $screen, 'Select country');
    else:
        showCitiesScreen($event, $event->object->text);
    endif;
    return;
}

function showCitiesScreen($event,$country, $screen = 1,$text = 'Select city'){
    $country_id = getCountryID($country);
    $cities = getCities($country_id);
    $citiesID = getCityIDs($country_id);
    $offset = 2000;
    $citiesK = new Keyboard();
    $citiesK->setParam(getParam($screen,$offset,$cities,$citiesID));
    $keyboard = $citiesK->getKeyboard();
    messageSendWK($event->object->from_id,$text, $keyboard);
    return;
}



function showCountriesScreen($uid, $screen = 1, $text = 'Select country'){
    $eu = getCountries();
    $euid = getCountriesID();
    $offset = 1000;
    $countries = new Keyboard();
    $countries->setParam(getParam($screen, $offset, $eu,$euid));
    $keyboard = $countries->getKeyboard();       
    messageSendWK($uid,$text, $keyboard);
    return;
}


function getNumbers($screen,$offset,$numbersArray){
    $prevScreen = $offset + 990 + $screen - 1;
    $nextScreen = $offset + 990 + $screen + 1;
    $start = ($screen -1)*7;
    $numbers = array_slice($numbersArray, $start, 7);
    foreach($numbers as &$number):
        $number = $number + $offset;
    endforeach;
    $last = array_pop($numbers);
    if($screen == 1 && count($numbers) == 6):
        array_push($numbers,$offset +990);
    elseif($screen == 1 && count($numbers) < 6):
        array_push($numbers,$last);
        array_push($numbers,$offset +990);
        return $numbers;
    elseif(count($numbers)< 6):
        array_push($numbers,$last);        
        array_push($numbers,$prevScreen);
        return $numbers;
    else:
        array_push($numbers,$prevScreen);
    endif;
    array_push($numbers,$last);
    if(empty($numbersArray[$start +8])):
        return $numbers;
    endif;
    array_push($numbers,$nextScreen);
    return $numbers;
}

function getLabels($screen,$labelsArray){
    $start = ($screen -1)*7;
    $labels = array_slice($labelsArray, $start, 7);
    $last = array_pop($labels);
    if($screen == 1 && count($labels) == 6):
        array_push($labels,'Back');
    elseif($screen == 1 && count($labels) < 6):
        array_push($labels,$last);
        array_push($labels,'Back');
        return $labels;
    elseif(count($labels)< 6):
        array_push($labels,$last);
        array_push($labels,'<');
        return $labels;
    else:
        array_push($labels,'<');
    endif;
    array_push($labels,$last);
    if(empty($labelsArray[$start + 8])):
        return $labels;
    endif;
    array_push($labels,'>');
    return $labels;
}


function getParam($screen, $offset, $labelsArray, $numbersArray){
    $numbers = getNumbers($screen,$offset,$numbersArray);
    $labels = getLabels($screen,$labelsArray);
    if(count($labels) < 9):
        $col = getColArray(count($labels));
        $row = ceil(count($labels)/3);
    else:
        $col = array(3,3,3);
        $row = 3;
    endif;
    $param = array(
            'row' => $row,
            'col' => $col,
            'numbers' => $numbers,
            'labels' => $labels,
            'colors' => [],
            'one_time' => false,
            'offset' =>$offset
        );
    return $param;
}

function getColArray($labels){
    $col = array();
    $i = intdiv($labels,3);
    while($i > 0):
        array_push($col,3);
        $i--;
    endwhile;
    array_push($col,$labels%3);
    return $col;
}


function messagesGetHistory($uid){
    $param = array(
        'user_id' =>$uid,
        'access_token' => GROUP_TOKEN,
        'v'=> VERSION,
        'count' => 200);
    return json_decode(file_get_contents(GET_HISTORY.http_build_query($param)))->response->items;
}


function messageSendWOK($uid,$messsage){
    $rid = rand(1, (int)pow(10,32));
    $param = array(
        'user_id' =>$uid,
        'access_token' => GROUP_TOKEN,
        'v'=> VERSION,
        'random_id' => $rid,
        'message' => $messsage);
    return file_get_contents(MESSAGE_SEND.http_build_query($param));
}


function messageSendWK($uid,$messsage,$keyboard){
    $rid = rand(1, (int)pow(10,32));
    $param = array(
        'user_id' =>$uid,
        'access_token' => GROUP_TOKEN,
        'random_id' => $rid,
        'message' => $messsage,
        'keyboard' => $keyboard,
        'v'=> VERSION);
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

function getCountries(){
    $eu = array();
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
    $sql = 'SELECT * FROM europe ORDER BY country_rus;';
    $stm = $pdo->prepare($sql);
    $stm ->execute();
    while($row = $stm->fetch()):
        array_push($eu, $row->country_rus);
    endwhile;
    return $eu;
}


function getCountriesID(){
    $eu = array();
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
    $sql = 'SELECT * FROM europe ORDER BY country_rus;';
    $stm = $pdo->prepare($sql);
    $stm ->execute();
    while($row = $stm->fetch()):
        array_push($eu, $row->id);
    endwhile;
    return $eu;
}




function getCountryID($name){
    $country = mb_strtoupper($name,'UTF-8');
    if(!in_array($country,getCountries())):
        return FALSE;
    endif;
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
    $sql = 'SELECT id FROM europe WHERE country_rus =?;';
    $stm = $pdo->prepare($sql);
    $stm ->execute(array($country));
    return $stm->fetch()->id;
}

function getCities($country_id){
    $cities = array();
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
    $sql = 'SELECT * FROM cities WHERE country_id =?;';
    $stm = $pdo->prepare($sql);
    $stm ->execute(array($country_id));
    while($row = $stm->fetch()):
        array_push($cities, (string)$row->city_rus);
    endwhile;
    return $cities;
}

function getCityIDs($country_id){
    $cities = array();
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
    $sql = 'SELECT * FROM cities WHERE country_id =?;';
    $stm = $pdo->prepare($sql);
    $stm ->execute(array($country_id));
    while($row = $stm->fetch()):
        array_push($cities, (string)$row->id);
    endwhile;
    return $cities;
}

function getCityID($city){
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
    $sql = 'SELECT * FROM cities WHERE city_rus =?;';
    $stm = $pdo->prepare($sql);
    $stm ->execute(array($city));
    return $stm->fetch()->id;
}

function getTitles($city_id){
    $titles = array();
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
    $sql = 'SELECT * FROM sputnik8 WHERE city_id =?;';
    $stm = $pdo->prepare($sql);
    $stm ->execute(array($city_id));
    while($row = $stm->fetch()):
        $title = (string)$row->title; 
        array_push($titles, $title);
    endwhile;
    return $titles;
}