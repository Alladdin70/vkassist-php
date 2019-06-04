<?php
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

$offers = array();
/*
 *   ЧИТАЕМ FEED
 */
$result = get_OnlinetoursXML();
/*
 *  ПАРСИМ XML
 */
$rss = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
/*
 *  ФИЛЬТРУЕМ ЕВРОПЕЙСКИЕ НАПРАВЛЕНИЯ И ОТПРАВЛЕНИЯ ИЗ МСК-СПБ
 */
foreach($rss->product as $item):    
    $country = (string)$item->category[1];
    $depart = (string)$item->category[0];
    if(in_array($country,EU)&&(in_array($depart,CAPITALS))):
        $offer = new Offer();
        $offer->country = $country;
        $offer->depart = $depart;
        $offer->cityArr = (string)$item->category[2];
//  ВЫТАСКИВАЕМ АДРЕС КАРТИНКИ
        $counter = strpos((string)$item->photo, "timestamp",0);
        $offer->picUrl = substr((string)$item->photo,0,$counter -1);
//  ВЫТАСКИВАЕМ СТОИМОСТЬ И ВАЛЮТУ ПРЕДЛОЖЕНИЯ
        $offer->price =  $item->price;
        $offer->currency = $item->currency;
//  ВЫТАСКИВАЕМ АДРЕС ДЛЯ ССЫЛКИ В ОПИСАНИИ ДОПОЛНЯЕМ ЕГО СВОИМИ АГЕНТСКИМИ ДАННЫМИ
        $offer->url = PROTOCOL.$item->url.P_MARKER;
//  ВЫТАСКИВАЕМ ИДЕНТИФИКАТОР ПРЕДЛОЖЕНИЯ
        $offer->ident = $item->identifier;
//  ДОБАВЛЕНИЕ ОБЪЕКТА - ПРЕДЛОЖЕНИЯ В МАССИВ        
        array_push($offers, $offer);
    endif;
endforeach;



foreach ($offers as $offer):
    echo '<br>';
    echo $offer->country.' '.$offer->depart. ' - '.$offer->cityArr;
    echo '<br>'; 
    echo $offer->price. ' '. $offer->currency;
    echo '<br>';
    echo "<img src=". $offer->picUrl. " width =\"640\" height=\"480\">";
    echo '<br>';
    echo $offer->url;
    echo '<br>';
    echo $offer->ident;
    echo '<br>';
endforeach;



class Offer{
    public $country;//Страна
    public $depart;//Город отправления
    public $cityArr;//Курорт
    public $picUrl;//Картинка
    public $price;//Цена
    public $currency;//Валюта предложения
    public $url;//Ссылка на предложение
    public $ident;// Идентификатор предложения
}

/*
 *  ВЫТАСКИВАЕМ FEED НА СВЕТ БОЖИЙ ПРИ ПОМОЩИ ВСКЕМОГУЩЕГО cURL И USERAGENT
 *  USERAGENT - ОБЯЗАТЕЛЬНАЯ ОПЦИЯ И БЕЗ ЕНГО НИФИГА НЕ РАБОТАЕТ
 */
function get_OnlinetoursXML(){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, ONLINETOURS_RU);
    curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($ch, CURLOPT_ENCODING,ACCEPT_ENC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, HEADER);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}