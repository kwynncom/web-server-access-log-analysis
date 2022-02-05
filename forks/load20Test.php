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

		$remn = $sz;
		
		for ($ri=0; $ri++ < self::maxCh && $rn < $sz; $ri++) {
			if ($remn > self::chunksb) $tor = self::chunksb;
			else					   $tor = $remn;
			
			fseek(     $r, $rn);
			$t = fread($r, $tor); kwas(kwifs($t, $tor - 1) !== false && kwifs($t, $tor) === false, 'bad read n 2317');
			$rn   += $tor;
			$remn -= $tor; unset($tor);
			
			$line = strtok($t, "\n");

			while ($line !== false) {
				# do something with $line
				$line = strtok("\n");
			} 
			
			continue;
		} 
		return; 
		
		
	}
	
	function read() {
		
	}

	
}

new load20_divide();