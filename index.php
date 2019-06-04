<?php
require_once 'globals.php';
require_once 'vkgroup.php';
require_once 'oauth.php';
session_start(); // Токен будем хранить в сессии
// Формируем ссылку для авторизации
$params = array(
	'client_id'     => CLIENT_ID,
	'redirect_uri'  => REDIRECT_URI,
	'response_type' => 'code',
	'v'             => VERSION, // (обязательный параметр) версия API, которую Вы используете https://vk.com/dev/versions
 
	// Права доступа приложения https://vk.com/dev/permissions
	// Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
	// Если не указать "offline", то полученный токен будет жить 12 часов.
	'scope'         => 'photos,offline',
);
echo '<a href="http://oauth.vk.com/authorize?' . http_build_query( $params ) . '">Авторизация через ВКонтакте</a>';
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
    echo getImageFileName($offer->picUrl);
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
