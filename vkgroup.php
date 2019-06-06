<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'globals.php';

function getUploadServer(){
    $param = array(
        'group_id' => GROUP_ID,
        'access_token' => ADMIN_TOKEN,
        'v' => VERSION
    );
    $response = file_get_contents(GET_WALL_UPLOAD_SERVER. http_build_query($param));
    return json_decode($response);   
}

function uploadImg($filename){
    $file = dirname(__FILE__).IMAGE_CAT.$filename;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, getUploadServer()->response->upload_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data; charset=UTF-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file1' => new CURLFile($file)]);
    $result = json_decode(curl_exec($ch),true);
    curl_close($ch);
    return $result;
}

function savePhotoVk($result){
    $response = file_get_contents(PHOTO_SAVE.$result["server"]."&photo"
            . "=".$result["photo"]."&hash=".$result["hash"]."&group_id"
            . "=".GROUP_ID."&access_token=".ADMIN_TOKEN."&v=".VERSION);
    return json_decode($response);
}



function savePhotoByUrl($url){
    $ch = curl_init($url);
    $filename = getImageFileName($url);
    $fp = fopen(IMAGE_PATH.$filename,'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $filename;
}

function deleteImage($filename){
    unlink(IMAGE_PATH.$filename);
    return;
}



function getImageFileName($url){
    $path = parse_url($url)["path"];
    $pathArray = explode("/", $path);
    $filename = $pathArray[count($pathArray)-1];
    return $filename;
}
function getPhoto($photoUrl){
    $fn = savePhotoByUrl($photoUrl);
    $result = savePhotoVk(uploadImg($fn));
    $owner_id = $result->response[0]->owner_id;
    $media_id = $result->response[0]->id;
    $media = 'photo';       
    deleteImage($fn);
    return $media.$owner_id.'_'.$media_id;
}