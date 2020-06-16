<?php
$array = scandir('F:\phpstudy_pro\WWW\www.dd-front.com\wxcomponents\vant\dist');

$l = [];
foreach ($array as $v){
    if ($v == '.' || $v == '..') continue;

    echo '"van-'.$v.'": "/wxcomponents/vant/dist/'.$v.'/index",'.PHP_EOL;
}
