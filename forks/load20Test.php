<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

define('KWYNN_INSERT_MANY_BUFFER_COUNT', 1000);

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
		$rn = 0;

		$b = [];

			
		$i = 0;
		while ($l = fgets($r)) {
			$t = ['l' => $l, 'n' => ++$i];
			$this->bufI($t, $this->lcoll);
		}

		$toti = $this->bufI(false, $this->lcoll);
		
		
		return; 
	}
	
	public static function bufI($d, $c) {
		static $b = [];
		static $i = 0;
		static $t = 0;
		static $bc = KWYNN_INSERT_MANY_BUFFER_COUNT;

		$ib = is_bool($d);
		
		if (!$ib) { $b[] = $d; $i++; }
		
		if (($i >= $bc) || ($ib && $i > 0))
		{ 
			$r = $c->insertMany($b); 
			kwas($r->getInsertedCount() === $i, 'bad bulk insert count kwutils 0240');
			$t += $i; $b = []; $i = 0;
		}	
		
		return $t;
	}

	
}

new load20_divide();