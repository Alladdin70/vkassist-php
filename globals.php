<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define('ONLINETOURS_RU','https://www.onlinetours.ru/affiliates/feed');
define('USERAGENT',$_SERVER ["HTTP_USER_AGENT"]);
define('ACCEPT_ENC',$_SERVER ["HTTP_ACCEPT_ENCODING"]);
define('HEADER',array( 'Expect:','Connection: Keep-Alive','Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.7' ));
define('EU',array('Турция', 'Болгария', 'Италия', 'Испания', 'Греция', 'Кипр',
    'Черногория', 'Чехия', 'Франция', 'Израиль', 'Грузия', 'Хорватия', 'Абхазия',
    'Мальта', 'Армения', 'Венгрия', 'Андорра', 'Бельгия', 'Нидерланды', 'Дания',
    'Швеция', 'Финляндия', 'Норвегия', 'Германия', 'Великобритания', 'Исландия',
    'Португалия', 'Сербия', 'Швейцария', 'Словакия', 'Польша', 'Румыния',
    'Эстония', 'Латвия', 'Литва', 'Словения', 'Австрия', 'Ирландиz', 'Азербайджан'));
define('CAPITALS',array("Москва","Санкт-Петербург"));
define('P_MARKER', '?advert=326&sub_id=926016');
define('PROTOCOL','http:');
//https://pixabay.com/api/?key=12683849-ab4c8a4c2f15229f7685ee3d7&q=travel+beach&image_type=photo&pretty=true&lang=ru api for pic
define('GROUP_TOKEN','19836e2c377bd86f0b11ae2abfdfaa9506d2b756dd817fa80e480d3a6e9829a13445118c512141ff1a6b8');
define('VERSION',5.95);
define('IMAGE_PATH', './img/');
define('IMAGE_CAT', '/img/');
define('GET_WALL_UPLOAD_SERVER','https://api.vk.com/method/photos.getWallUploadServer?');
define('PHOTO_SAVE','https://api.vk.com/method/photos.saveWallPhoto?server=');
define('CLIENT_ID','7009711');
define('CLIENT_SECRET','8whzA6PXaNIQO8a4Xg1g');
define('REDIRECT_URI','https://vkassist.ru/oauth.php');
define('ADMIN_TOKEN','85d9f02d7371d478726a5b89fadaf1895a91707dbd7369cd01ca5bac13ca5ea3824917ac8a84506668cfd');
define('GROUP_ID', 137527828);


