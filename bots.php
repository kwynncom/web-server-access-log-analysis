<?php

function isBot($s) { // 2021/03/11 edition, revising 03/12
    
    static $ba = [];
    static $m5k = 'Mozilla/5.0';
    static $m5l = 11; // assuming Mozilla/5.0
    
    if ($s === '-') return true;
    if (substr($s, 0, $m5l) !== $m5k) return true;
    
    if (!$ba) $ba = [ 
	'://www.semrush.com/bot.html',
	'://aspiegel.com/petalbot',
	'://www.opensiteexplorer.org/dotbot',
	'://ahrefs.com/robot',
	'://www.google.com/bot.html',
	'://webmeup-crawler.com',
	'://mj12bot.com/',
	'://www.bing.com/bingbot.htm',
	'Nimbostratus-Bot',
	'://www.apple.com/go/applebot',
	'://babbar.tech/crawler',
	'://www.xforce-security.com/crawler/',
	'Seekport Crawler; http://seekport.com/',
	'://yandex.com/bots',
	'yunjiankong',
	'://napoveda.seznam.cz/en/seznambot-intro/',
	
	];
    
    foreach($ba as $b) if (sp($s, $b)) return true;
    
    $res = ['/Adsbot\/\d+\.\d+/', '/zgrab\/\d+\.\w+/'];
    foreach($res as $re) 
	if (preg_match($re , $s)) return true;
	
    
    return false;
}
function sp($h, $n) {
    $r = strpos($h, $n);
    if ($r !== false) return true;
    return false;
}