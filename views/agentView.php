<?php

function sr($s, $r, $sub) { return str_replace($s, $r, $sub); }

class agent_view_10 {
	
	const C81 = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36';
	
	public static function f15($s) {
		$s = str_replace('Mozilla/5.0 ', '', $s);
		if (!preg_match('/^\([^\)]+\)/', $s, $ms10)) return $s;
		if (preg_match('/iPhone OS (\d[_\d]+)/', $s, $ms20)) $s = str_replace($ms10[0], 'iPh ' . $ms20[1], $s);
		
		
		return $s;		
		
		
	}
	
	public static function f20($ain) {
		$s = str_replace('Mozilla/5.0 ', '', $ain);
		if (preg_match('/^\((iPhone[^\)]+\))/', $s, $mso))
			if (preg_match('/iPhone OS (\d[_\d]+)/', $s, $ms)) { 
				$s = str_replace($mso[0], 'iPh ' . $ms[1], $s);
			}
			
		$s = str_replace('AppleWebKit/', 'AWK/', $s);
		$s = sr(' (KHTML, like Gecko) ', ' ', $s);
		$s = sr('Mobile/', 'Mob/', $s);
		$s = sr('Version', 'v', $s);
		$s = sr('Safari', 'Saf', $s);
		return $s;
		
	}
	
	public static function filter($a) {
		return self::f15($a);
	}
	
	public static function isBot($a, $u) { 
		if ($a === self::C81 && $u === '/') return true;
		return false;

	}
	
}
