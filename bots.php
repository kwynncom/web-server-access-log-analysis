<?php
function isBot($ua) {
    
    $sp = [
	'http://ahrefs.com/robot/',
	'https://webmaster.petalsearch.com/site/petalbot',
	'https://opensiteexplorer.org/dotbot',
	'http://webmeup-crawler.com/',
	'https://aspiegel.com/petalbot',
	'http://mj12bot.com/',
	'http://www.opensiteexplorer.org/dotbot',
	'http://www.semrush.com/bot.html',
	'http://megaindex.com/crawler',
	'MauiBot (crawler.feedback+wc@gmail.com)',
	'http://serpstatbot.com/',
	'https://developer.amazon.com/support/amazonbot',
	'http://seekport.com/',
	'Sogou web spider',
	'https://babbar.tech/crawler',
	'http://www.bing.com/bingbot.htm',
	'http://www.google.com/bot.html',
	'http://www.linguee.com/bot',
	'https://seostar.co/robot/',
	'http://yandex.com/bots',
	'http://napoveda.seznam.cz/en/seznambot-intro/',
	'Go-http-client',
	'internal dummy connection',
	'https://seostar.co/robot/',
	'https://about.censys.io/',
	'http://www.apple.com/go/applebot',
	'http://cs.daum.net/',
	'http://cloudsystemnetworks.com',
	'Apache-HttpClient',
	'http://go.mail.ru/help/robots',
	'python-requests',
	'l9explore',
	'fasthttp',
	'https://commoncrawl.org/',
	'http://www.baidu.com/search/spider.html',
	'netsystemsresearch.com',
	'http://megaindex.com/crawler',

    ];
		
    foreach($sp as $i) if (strpos($ua, $i) !== false) return true; unset($i, $sp);

    $re = ['/Adsbot\/\d+\.\d+\W*/',
	    '/zgrab\/\w+\.\w+\W*/',
	'/Googlebot-Image\/\d+\.\d+\W*/',
	'/^axios\/[\d+\.]+$/',
	'/^curl\/[\d+\.]+$/',	
	'/^PycURL\/[\d+\.]+/',	
	'/^Twitterbot\/[\d+\.]+$/', // note Facebook external hit question
	    ];	

    foreach($re as $i) if (preg_match($i, $ua)) return true; unset($re, $i);

    $eq = [
	    'Mozilla/5.0 Jorgee', 
	    '-',
	    'The Knowledge AI',
	    'ZmEu',
	    'Linux Gnu (cow)',
	    'Twitterbot',
	    'TelegramBot (like TwitterBot)',
	];

    foreach($eq as $i) if ($ua === $i) return true; unset($eq, $i);
    
    $ci = [
	    'Hello, world'
	];
    
    foreach($ci as $i) if (strtolower($ua) === strtolower($i)) return true; unset($ci, $i);
    
    unset($ua); // OCD
    return false;
}
