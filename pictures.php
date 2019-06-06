<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*

require_once 'globals.php';
session_start(); // Токен будем хранить в сессии
if(!isset($_SESSION['token'])):
//    session_start(); // Токен будем хранить в сессии
// Формируем ссылку для авторизации
    $params = array(
	'client_id'     => CLIENT_ID,
	'redirect_uri'  => REDIRECT_URI,
	'response_type' => 'code',
	'v'             => VERSION, // (обязательный параметр) версия API, которую Вы используете https://vk.com/dev/versions
 
	// Права доступа приложения https://vk.com/dev/permissions
	// Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
	// Если не указать "offline", то полученный токен будет жить 12 часов.
	'scope'         => 'photos,friends,audio,video,pages,status,wall,ads,docs,groups,stats,email,market,offline',
    );
    $requestStr = "http://oauth.vk.com/authorize?" . http_build_query( $params );
    //echo '<a href="'.$requestStr . '">Авторизация через ВКонтакте</a>';
    header('Location:'.$requestStr);
endif;*/
/*$min = 1;
$max = 592;
for($i=0; $i<50; $i++):
    echo random_int($min, $max);
    echo '<br>';
endfor;

require_once('PHPExcel.php');
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

$file = dirname(__FILE__).'/apinfo.xls';
$xls = PHPExcel_IOFactory::load($file);
for($i=1;$i<593;$i++):
    $city_rus=$xls->getActiveSheet()->getCell('A'.$i)->getValue();
    $city_eng=$xls->getActiveSheet()->getCell('B'.$i)->getValue();
    $country_rus=$xls->getActiveSheet()->getCell('D'.$i)->getValue();
    $country_eng=$xls->getActiveSheet()->getCell('E'.$i)->getValue();
    $sql = "INSERT INTO picdata VALUES(default, ?,?,?,?);";
    $stm = $pdo->prepare($sql);
    $stm->execute([$city_rus, $city_eng,$country_rus,$country_eng]);   
endfor;

echo 'done records - '.$i;

*/
$maximum = 592;
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
$id = rand(1, $maximum);

$sql = "SELECT * FROM picdata WHERE id=?;";
$stm = $pdo->prepare($sql);
$stm->execute(array($id));
$picData = $stm->fetch();
$findStr = $picData->city_eng.'+'.$picData->country_eng;
$requestStr = 'https://pixabay.com/api/?key=12683849-ab4c8a4c2f15229f7685ee3d7&q=architecture+'.$findStr.'&image_type=photo';
$response = json_decode(file_get_contents($requestStr));
$totalHits = $response->totalHits;
if($totalHits ==0):
    $findStr = 'landscape+'.$picData->country_eng;
    $requestStr = 'https://pixabay.com/api/?key=12683849-ab4c8a4c2f15229f7685ee3d7&q='.$findStr.'&image_type=photo';
    $response = json_decode(file_get_contents($requestStr));
    $totalHits = $response->totalHits;
    $description = $picData->country_rus;
else:
    $description = $picData->country_rus.', '.$picData->city_rus;
endif;
switch($totalHits):
    case $totalHits == 1:
        try{
        $url = $response->hits[0]->largeImageURL;
        }
        catch(Exception $ex){
            header( 'Refresh:url=' . $_SERVER['PHP_SELF'] );
        }
        break;
    case $totalHits > 10:
        $offset = rand(1,10)-1;
        try{
        $url = $response->hits[$offset]->largeImageURL;
        }
        catch(Exception $ex){
            header( 'Refresh:url=' . $_SERVER['PHP_SELF'] );
        }
        break;
    default:
        $offset = rand(1,$totalHits)-1;
        try{
        $url = $response->hits[$offset]->largeImageURL;
        }
        catch(Exception $ex){
            header( 'Refresh:url=' . $_SERVER['PHP_SELF'] );
        }
        break;
endswitch;

echo $description;
echo "<br>";

echo '<img src='.$url.'>';

header( 'Refresh:5; url=' . $_SERVER['PHP_SELF'] );

//header('location:index.php');
