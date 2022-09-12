<?php

require_once(__DIR__ . '/../utils/parse.php');

class manageOverlap {
	
	const maxIntervalMin = 25;  // based on experiment with "gaps.php"
	const maxIntervalS = self::maxIntervalMin * 60 ;
	const maxIntervalus  = self::maxIntervalS * M_MILLION;
	
	public function __construct() { 
		$this->gotnew = false;
		
	}
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
		$ll = $a[$ca - 1];
		$this->lasttsus = wsal_parse::parse($ll, true, true);
		echo("last line from copy:\n$ll\n");
		return;
	}
	
	public function getNew(string $tin, bool $failok = false) {
		
		if ($this->gotnew) return $tin;
		
		$uqi = strpos($tin, $this->uqt); 
		if ($uqi === false && $failok) return '';
		kwas($uqi !== false, 'unique text not found wsal overlap');
		$nt  = substr($tin, $uqi + strlen($this->uqt));
		$fl  = strtok($nt, "\n");
		echo("First new line\n$fl\n");
		$tsus = wsal_parse::parse($fl, true, true);	
		$d    = $tsus - $this->lasttsus; kwas($d >= 0, 'bad overlap - lesser ts - wsal');
		kwas($d <= self::maxIntervalus, 'overlap interval too long based on experiment');
		echo(sprintf('%0.2f', $d / M_MILLION) . 's gap' . "\n" );
		
		$ll = $fl;
		$ls = 1;
		while($tl = strtok("\n")) {
			$ll = $tl;
			$ls++;
		}
		
		echo("Last line added\n$ll\n");
		
		echo("$ls lines added\n");
		$by = strlen($nt);
		echo("$by bytes added\n");
		echo(roint($by / $ls) . ' bytes per line added' . "\n");
		$this->gotnew = true;
		return $nt;
	}
		
}