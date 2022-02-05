<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class load20_divide extends dao_generic_3 {
	
	// const lfin = '/tmp/access.log';
	const lfin = '/tmp/a26.log';
	const chunksM = 10;
	const chunksb = self::chunksM * M_MILLION;
	const maxCh   = 500;
	const ckchunks = 10000;
	
	function __construct() {
		parent::__construct('wsal20');
		$this->creTabs(['l' => 'lines']);
		kwas(is_readable(self::lfin), 'file not readable');
		$sz = $this->thesz = filesize(self::lfin);
		$rs = multi_core_ranges::get(1, $sz);
		$r = fopen(self::lfin, 'r');
		$rn = 0;

		$remn = $sz;
		$b = [];
			
		for ($ri=0, $li=0; $ri++ < self::maxCh && $rn < $sz; $ri++) {
			if ($remn > self::chunksb) $tor = self::chunksb;
			else					   $tor = $remn;
			
			$i = 0;
			while ($l = fgets($r)) {
				$b[] = ['l' => $l, 'n' => ++$i];
				if (count($b) >= self::ckchunks) { $this->lcoll->insertMany($b); $b = []; }
			}
			
			if (count($b) > 0) { $this->lcoll->insertMany($b); $b = []; }
			
		} 
		

		
		return; 
		
		
	}
	
	function read() {
		
	}

	
}

new load20_divide();