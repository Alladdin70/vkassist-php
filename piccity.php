<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$i = 0;
$en = array();
$rus = array();
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
$sql = "SELECT * FROM picdata;";
$stm = $pdo->prepare($sql);
$stm->execute();
while($picData = $stm->fetch()):
    if(!in_array($picData->country_eng, $en)):
        array_push($en, $picData->country_eng);
        array_push($rus, $picData->country_rus);
        $i++;
    endif;
endwhile;
echo 'Total rows - '.$i;
for($j = 0; $j < $i; $j++):
    $sql="INSERT INTO countries VALUES(default, ?, ?);";
    $data = array($rus[$j],$en[$j]);
    $stm = $pdo->prepare($sql);
    $stm->execute($data);
endfor;
echo '<br>';
echo 'Total rows - '.$j;

function saveCity($picData,$totalHits){
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
    $sql = "INSERT INTO piccity VALUES(default, ?, ?, ?, ?, ?);";
    $data = array($picData->city_rus, $picData->city_eng, $picData->country_rus,
        $picData->country_eng, $totalHits);
    $stm = $pdo->prepare($sql);
    $stm->execute($data);
    return;
}