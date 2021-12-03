<?php

function stm($h, $n) { return !(strpos($h, $n) === false); }

class wsal_bots {
	public static function botPercentage($ain) {
		if (self::precisely($ain)) return 100;
		if (stm($ain, 'external')) return 0;
		if (self::ifSubstr($ain)) return 100;
		if (self::pandre10($ain)) return 100;
		if (self::loose10neq($ain))  return  80;
		if (self::loose20eq ($ain))  return  90;
		return 0;
	}
	
	private static function loose20eq($ain) {	
		$a = ['http://', 'https://'];
		foreach($a as $r) if (stm($ain, $r)) return TRUE;
		return FALSE;
	}
	
	private static function loose10neq($ain) {
		$a = ['Mozilla/5.0', 'Mozlila/5.0', 'Opera']; // the missspelling looks otherwise legit
		foreach($a as $r) if (substr($ain, 0, strlen($r)) === $r) return FALSE;
		return TRUE;
	}
	
	private static function precisely($ain) {
		$a = ['-', 'Ktor client', 'clark-crawler2/Nutch-1.19-SNAPSHOT', 'ADATools/1.0.0',
			'Sogou web spider/4.0(+http://www.sogou.com/docs/help/webmasters.htm#07)', 'Mozilla/5.0 zgrab/0.x',
			'Mozilla/5.0 (compatible; SeznamBot/3.2; +http://napoveda.seznam.cz/en/seznambot-intro/)',
			'Mozilla/5.0 (compatible;PetalBot;+https://webmaster.petalsearch.com/site/petalbot)'];
		foreach($a as $r) if ($ain === $r) return TRUE;
		return FALSE;
	}
	
	private static function ifSubstr($ain) {
		$ss = ['(compatible; PetalBot;+https://webmaster.petalsearch.com/site/petalbot)'];
		foreach($ss as $s) if (stm($ain, $s)) return TRUE;
		return FALSE;
	}
	
	private static function pandre10($ain) {
		$a = [
			['(compatible; AhrefsBot/'		,'v','; +http://ahrefs.com/robot/)'],
			['(compatible; DotBot/'			,'v','; +https://opensiteexplorer.org/dotbot; help@moz.com)'],
			['(compatible; BLEXBot/'		,'v','; +http://webmeup-crawler.com/)'],
			['(compatible; SemrushBot/'		,'v','; +http://www.semrush.com/bot.html)'],
			['(compatible; DataForSeoBot/'	,'v','; +https://dataforseo.com/dataforseo-bot)'],
			['(compatible; MJ12bot/v'		,'v','; http://mj12bot.com/)'],
			['(compatible; Barkrowler/'     ,'v','; +https://babbar.tech/crawler)'],
			['(compatible; bingbot/'        ,'v','; +http://www.bing.com/bingbot.htm)'],
			['(compatible; Googlebot/'		,'v','; +http://www.google.com/bot.html)'],
			['(compatible; YandexBot/'      ,'v','; +http://yandex.com/bots)']
		];
		
		$h = $ain;
		foreach($a as $r) {
			foreach($r as $part) {
				if ($part !== 'v') {
					$sp = strpos($h, $part);
					if ($sp === false) continue 2;
					$h = substr($h, $sp + strlen($part));
				} else {
					if (!preg_match('/^\d[^;]*/', $h, $ms)) continue 2;
					$h = substr($h, strlen($ms[0]));
				}
			}
			
			return TRUE;
		}
		
		return FALSE;
				
	}
}

