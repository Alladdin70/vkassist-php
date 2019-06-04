<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



require_once 'globals.php';




function getImageFileName($url){
    $path = parse_url($url)["path"];
    $pathArray = explode("/", $path);
    $filename = $pathArray[count($pathArray)-1];
    return $filename;
}
