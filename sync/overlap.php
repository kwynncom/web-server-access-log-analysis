<?php

require_once(__DIR__ . '/../utils/parse.php');

class manageOverlap {
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
		$this->lastts = wsal_parse::parse($a[$ca - 1], true);
		return;
	}
		
}