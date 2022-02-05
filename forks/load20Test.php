<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class load20_divide {
	
	const lfin = '/tmp/a26.log';
	const chunksM = 10;
	const chunksb = self::chunksM * M_MILLION;
	const maxCh   = 500;
	
	function __construct() {
		kwas(is_readable(self::lfin), 'file not readable');
		$sz = $this->thesz = filesize(self::lfin);
		$rs = multi_core_ranges::get(1, $sz);
		$r = fopen(self::lfin, 'r');
		$rn = 0;
		$si = 0;
		$ii = 0;
		$remn = $sz;
		$remsa = [];
		
		while($si++ < self::maxCh && $rn < $sz) {
			if ($remn > self::chunksb) $tor = self::chunksb;
			else					   $tor = $remn;
			
			$t = fread($r, $tor); kwas(kwifs($t, $tor - 1) !== false && kwifs($t, $tor) === false, 'bad read n 2317');
			$rn   += $tor;
			$remn -= $tor; unset($tor);
			
			
			$a = explode("\n", $t); unset($t);
			if ($ii > 0) {
				$remsa[$rn]['f'] = $a[0]; 
				unset(			   $a[0]);
			}
			$lan = count($a) - 1;
			$remsa[$rn]['l'] = $a[$lan];
			unset(			   $a[$lan]); unset($lan);
	
			
			$ii++;
			continue;
		} 
		return; 
		
		
	}

	
}

new load20_divide();