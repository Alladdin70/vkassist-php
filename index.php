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
header('location:index2.php');
