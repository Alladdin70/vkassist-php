<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'globals.php';

if(isset($_GET['code'])):
    $params = array(
		'client_id'     => CLIENT_ID,
		'client_secret' => CLIENT_SECRET,
		'code'          => $_GET['code'],
		'redirect_uri'  => REDIRECT_URI
	);
    if (!$content = @file_get_contents('https://oauth.vk.com/access_'
            .'token?'. http_build_query($params))):
	$error = error_get_last();
	throw new Exception('HTTP request failed. Error: ' . $error['message']);
    endif;
    $response = json_decode($content);
 
	// Если при получении токена произошла ошибка
    if (isset($response->error)):
	throw new Exception('При получении токена произошла ошибка. Error: '
                . '' . $response->error . '. Error description:'
                . ' ' . $response->error_description);
    endif;
 
	$token = $response->access_token; // Токен
	$expiresIn = $response->expires_in; // Время жизни токена
	$userId = $response->user_id; // ID авторизовавшегося пользователя
 
	// Сохраняем токен в сессии
	$_SESSION['token'] = $token;
        var_dump($_SESSION);
        echo '<br>';
elseif ( isset( $_GET['error'] ) ): // Если при авторизации произошла ошибка
 
    throw new Exception( 'При авторизации произошла ошибка. Error: ' . $_GET['error']
	        . '. Error reason: ' . $_GET['error_reason']
	        . '. Error description: ' . $_GET['error_description'] );

endif;
