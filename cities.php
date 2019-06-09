<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$request = 'https://api.sputnik8.com/v1/cities/?api_key=2456d68dc4ef21630b12306'
        . '3b2c2f94e&username=sergey.repin@mail.ru';
$cities = json_decode(file_get_contents($request));
$i = 0;
foreach($cities as $city):
    if(in_array($city->country_id, $eu)):
        $name = mb_strtoupper($city->name,'UTF-8');
        $data = array($city->id, $city->country_id,$name);
        $sql = 'INSERT INTO cities VALUES(?, ?, ?);';
        $stm = $pdo->prepare($sql);
        $stm ->execute($data);
        $i++;
    endif;
endforeach;

echo 'done records - '.$i;