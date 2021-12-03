<?php

function stm($h, $n) { return !(strpos($h, $n) === false); }

class wsal_bots {
	public static function isBot($ain) {
		if (stm($ain, 'external')) return FALSE;
		if (self::preciseList($ain)) return TRUE;
	}
	
	private static function preciseList($ain) {
		$ss = ['(compatible; PetalBot;+https://webmaster.petalsearch.com/site/petalbot)'];
		foreach($ss as $s) if (stm($ain, $s)) return TRUE;
		return FALSE;
	}
}

