<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class load20_divide {
	
	const lfin = '/tmp/access.log';
	const chunksM = 10;
	const maxCh   = 500;
	
	function __construct() {
		kwas(is_readable(self::lfin), 'file not readable');
		$sz = $this->thesz = filesize(self::lfin);
		$rs = multi_core_ranges::get(1, $sz);
		// $r = fopen($self::lfin, 'r');
		$chb = self::chunksM * M_MILLION;
		$ri = 0;
		$ii = 0;
		$si = 0;
		do {
			
		} while($si++ < self::maxCh);
		return; 
		
		
	}

	
}

new load20_divide();