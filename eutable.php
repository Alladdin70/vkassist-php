<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'globals.php';
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

$file = dirname(__FILE__).'/citycode.xls';
$xls = PHPExcel_IOFactory::load($file);
for($i=1;$i<38;$i++):
    $country_rus=$xls->getActiveSheet()->getCell('C'.$i)->getValue();
    $uid=$xls->getActiveSheet()->getCell('A'.$i)->getValue();
    $country_eng=$xls->getActiveSheet()->getCell('B'.$i)->getValue();
    $country = mb_strtoupper($country_rus,'UTF-8');
    $country_e = mb_strtoupper($country_eng,'UTF-8');        
    $sql = "INSERT INTO europe VALUES(?, ?, ?);";
    $stm = $pdo->prepare($sql);
    $stm->execute([$uid,$country_e,$country]);   
endfor;

echo 'done records - '.$i;
