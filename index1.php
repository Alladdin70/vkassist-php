<?php

/*   https://ru.pirates.travel/feed
 * 
 * 
 */
$url = 'https://ru.pirates.travel/feed';
$content = file_get_contents($url);
$rss = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
foreach($rss->channel->item as $item){
    foreach($item->category as $category){
        if(!strcasecmp($category, "Европа")){
            print($item->title);
            print "<br>";
            print($item->link);
            print "<br>";
        }
    }
}



