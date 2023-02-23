<?php

function sr(string $sea, string $rep) { wsalCLIVisFilter::$ca = str_replace($sea, $rep, wsalCLIVisFilter::$ca); }

class wsalCLIVisFilter {
	
	public static $ca;
	
	public static function url(string &$u) { $u = preg_replace('/\?\S*/', ' ', $u); return $u; 	}
	
	public static function agent(string $a) {
		
		self::$ca = $a; unset($a);
		self::agent10();
		self::agent20();
		return self::$ca;
		
	}
	
	private static function agent20() {
		// Firefox \d\.\d
		preg_match('/(rv:(\d\.\d))\s+Firefox\/(\d+\.\d+)/', self::$ca, $ms);
		if ($ms) {
			if ($ms[2] === $ms[3]) {
				sr($ms[1], '');
				// exit(0);
			}
			// exit(0);
		}
	}
	
	private static function agent10() {
		sr('Mozilla/5.0 ', ' ');
		sr(' (KHTML, like Gecko) ', ' ');
		sr('(Windows NT 10.0; Win64; x64', 'Win10.0');
		sr('(', '');
		sr(')', '');
		sr(' like Mac OS X', '');
		sr('AppleWebKit/537.36', '');
		sr('Safari/537.36', '');
		sr('Gecko/20100101', '');
		sr('X11; Ubuntu; Linux x86_64;', 'X11Ubu');		
	}
}
