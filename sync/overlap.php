<?php

require_once(__DIR__ . '/../utils/parse.php');

class manageOverlap {
	
	const maxIntervalMin = 25;  // based on experiment with "gaps.php"
	const maxIntervalus  = self::maxIntervalMin * 60 * M_MILLION;
	
	public function __construct() { }
	public function setCopy(string $t) {

		$a = explode("\n", $t);
		$ca = count($a);
		$lbl = $a[$ca - 1]; kwas($lbl === '', 'expect last to be blank wsal overlap');
		unset( $a[$ca - 1]);
		$ca = count($a);
		kwas($ca >= 2, 'manageOverlap must have 2+ lines');
		$ui = -1;
		for ($i = $ca - 1; $i >= 1; $i--) {
			if ($a[$i] !== $a[$i - 1]) {
				$ui = $i - 1;
				break;
			}
		} kwas($ui >= 0, 'cannot find unique wsal line manageO');

		$ut = '';		
		for($i = $ui; $i < $ca; $i++) $ut .= $a[$i] . "\n";
		$this->uqt = $ut;
		$this->lasttsus = wsal_parse::parse($a[$ca - 1], true, true);
		return;
	}
	
	public function getNew(string $tin) {
		$uqi = strpos($tin, $this->uqt); kwas($uqi !== false, 'unique text not found wsal overlap');
		$nt  = substr($tin, $uqi + strlen($this->uqt));
		$fl  = strtok($nt, "\n");
		$tsus = wsal_parse::parse($fl, true, true);	
		$d    = $tsus - $this->lasttsus; kwas($d >= 0, 'bad overlap - lesser ts - wsal');
		kwas($d <= self::maxIntervalus, 'overlap interval too long based on experiment');
		return $nt;
	}
		
}