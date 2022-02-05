<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class load20_divide extends dao_generic_3 {
	
	// const lfin = '/tmp/access.log';
	// const lfin = '/tmp/a91.log';
	
	const lfin = '/tmp/a26.log';
	
	function __construct() {
		parent::__construct('wsal20');
		$this->creTabs(['l' => 'lines']);
		kwas(is_readable(self::lfin), 'file not readable');
		$sz = $this->thesz = filesize(self::lfin);
		$rs = multi_core_ranges::get(1, $sz);
		$r = fopen(self::lfin, 'r');
		$i = 0;
		while ($l = fgets($r)) {
			$t = ['l' => $l, 'n' => ++$i];
			inonebuf($t, $this->lcoll);
		}

		$toti = inonebuf(false, $this->lcoll);
		
		return; 
	}
	
}

new load20_divide();
