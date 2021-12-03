<?php

function stm($h, $n) { return !(strpos($h, $n) === false); }

class wsal_bots {
	public static function isBot($ain) {
		if (self::precisely($ain)) return TRUE;
		if (stm($ain, 'external')) return FALSE;
		if (self::ifSubstr($ain)) return TRUE;
		if (self::pandre10($ain)) return TRUE;
		return FALSE;
	}
	
	private static function precisely($ain) {
		$a = ['-', 'Ktor client', 'clark-crawler2/Nutch-1.19-SNAPSHOT', 'ADATools/1.0.0',
			'Sogou web spider/4.0(+http://www.sogou.com/docs/help/webmasters.htm#07)'];
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
			['(compatible; YandexBot/'      ,'v', '; +http://yandex.com/bots)']
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

