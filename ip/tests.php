<?php

$raw = '2600:8800::';
$low  = expand($ip);
$high = '2600:880F:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF';

$ip = '2600:8801:1c81:1300:11d3:e838:ad39:e13a';

// 2600:8800:: - 2600:880F:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF

function expand($ip){
    $hex = unpack("H*hex", inet_pton($ip));         
    $ip = substr(preg_replace("/([A-f0-9]{4})/", "$1:", $hex['hex']), 0, -1);

    return $ip;
} // https://stackoverflow.com/questions/12095835/quick-way-of-expanding-ipv6-addresses-with-php

