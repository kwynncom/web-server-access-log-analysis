<?php

function sr($s, $r, $sub) { return str_replace($s, $r, $sub); }

class agent_view_10 {
	
	const C81 = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36';
	
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
	
	public static function filter($a, $url) {
		static $ba = ['AhrefsBot/7.0' => 'ARB', 
		 'PetalBot;+https://webmaster.petalsearch.com/site/petalbot' => 'PetB',
		'SemrushBot/7~bl; +http://www.semrush.com/bot.html' => 'SRB',
		'ADATools/1.0.0' => 'AdaB', 
		self::C81 => 'LuxDesk Chrome/81...', 
		'+http://www.google.com/bot.html' => 'GooB', 
		'+https://opensiteexplorer.org/dotbot' => 'DotB',
		'+http://yandex.com/bots' => 'YaB',
		'TomcatBypass/Command/Base64' => 'Tomcat hack']; 
		
		if ($a === self::C81 && $url === '/') return 'AWSB';
		
		foreach($ba as $aa => $rv) {
		
			if (strpos($a, $aa) !== false) return $rv;
		}
		
		// return substr($a, 0, 130);
		return self::f20($a);
	}
	
}
