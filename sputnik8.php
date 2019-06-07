<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$europe = array(4, 5, 6, 7, 8, 9, 10,13, 14, 16, 17, 21, 22, 23, 26, 28, 30, 31,
    32, 33, 35, 37, 38, 40, 42, 46, 49, 52, 53, 55, 57, 58, 59, 69, 73, 76, 79, 80);
$page = 18;
$request = 'https://api.sputnik8.com/v1/products?api_key=2456d68dc4ef21630b12306'
        . '3b2c2f94e&username=sergey.repin@mail.ru&page='.$page.'';
$response= file_get_contents($request);
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
while(strlen($response)>2):
    $content = json_decode($response);
    foreach($content as $item):
        if(in_array($item->country_id, $europe)):
            $data = array(
                (int)$item->id,
                $item->title,
                $item->description,
                $item->url,
                $item->netto_price,
                (int)$item->city_id,
                $item->city_slug,
                (int)$item->country_id,
                $item->country_slug,
                $item->languages[0]
            );
            $sql="INSERT INTO sputnik8 VALUES(default, ?, ?, ?, ?, ?, ?, ?,"
                    . "default, ?, ?, default, ?);";
            $stm = $pdo->prepare($sql);
            $stm->execute($data);
        endif;
    endforeach;
    $page = $page + 1;
    echo $page."<br>";
    $request = 'https://api.sputnik8.com/v1/products?api_key=2456d68dc4ef21630b12306'
        . '3b2c2f94e&username=sergey.repin@mail.ru&page='.$page.'';
    $response= file_get_contents($request);
endwhile;

var_dump($page);
