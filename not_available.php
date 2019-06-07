<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$request = 'https://api.sputnik8.com/v1/products/not_available?api_key=2456d68d'
        . 'c4ef21630b123063b2c2f94e&username=sergey.repin@mail.ru';
$response = json_decode(file_get_contents($request));
$i = 0;
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
foreach($response as $item):    
    $sql = 'INSERT INTO not_available VALUES(default, ?);';
    $stm = $pdo->prepare($sql);
    $stm->execute([$item]);   
    $i++;
endforeach;
echo 'Total records - '.$i;