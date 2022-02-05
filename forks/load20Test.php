<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class load20_divide {
	
	const lfin = '/tmp/access.log';
	const chunksM = 10;
	const chunksb = self::chunksM * M_MILLION;
	const maxCh   = 500;
	
	function __construct() {
		kwas(is_readable(self::lfin), 'file not readable');
		$sz = $this->thesz = filesize(self::lfin);
		$rs = multi_core_ranges::get(1, $sz);
		$r = fopen(self::lfin, 'r');
		$rn = 0;
		$ii = 0;
		$si = 0;
		$remn = $sz;
		
		while($si++ < self::maxCh && $rn < $sz) {
			if ($remn > self::chunksb) $tor = self::chunksb;
			else					   $tor = $remn;
			
			$t = fread($r, $tor);
			try {
				kwas(kwifs($t, $tor - 1) !== false && kwifs($t, $tor) === false, 'bad read n 2317');
			} catch(Exception $ex) {
				$l = strlen($t);
				throw $ex;
			}
			$rn   += $tor;
			$remn -= $tor;
	
			
			continue;
		} 
		return; 
		
		
	}

	
}

new load20_divide();