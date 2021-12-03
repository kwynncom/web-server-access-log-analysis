<?php

function stm($h, $n) { return !(strpos($h, $n) === false); }

class wsal_bots {
	public static function isBot($ain) {
		if (stm($ain, 'external')) return FALSE;
		if (self::preciseList($ain)) return TRUE;
		if (self::pandre($ain)) return TRUE;
		return FALSE;
	}
	
	private static function preciseList($ain) {
		$ss = ['(compatible; PetalBot;+https://webmaster.petalsearch.com/site/petalbot)'];
		foreach($ss as $s) if (stm($ain, $s)) return TRUE;
		return FALSE;
	}
	
	private static function pandre($ain) {
		$a = [
			['(compatible; AhrefsBot/','v','; +http://ahrefs.com/robot/)']
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

