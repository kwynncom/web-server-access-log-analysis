<?php

require_once('db.php');
require_once('file.php');

class wsal_verify implements fork_worker {

public static function shouldSplit (int $low, int $high, int $cpuCount) : bool {
	return true;
}
public static function workit	  (int $low, int $high, int $workerN, ...$aa) {
	$aa = $aa[0];
	
	switch($workerN) {
		case 0: $o = $aa[1]; break;
		case 1: $o = $aa[2]; break;
		default: return; break;
	}
	
	$o->getHash($aa[0]);
}
	
	
public function __construct(  $db, $c, $f, $ts, $sz, $bpr, $epr, $isl) {
	$dbo = new wsal_verify_db($db, $c,     $ts,      $bpr, $epr, $isl);
	$this->di05($sz);
	$fo = new wsal_verify_fi($f, $isl, $dbo->getCounts());
	$fvp = '/tmp/wsal_v_' . dao_generic_3::get_oids();
	
	fork::dofork(true, 1, 2, 'wsal_verify', $fvp, $dbo, $fo);
	// $dbh = $dbo->getHash($fvp);
	// $fih = $fo->getHash($fvp);
	// $this->cmp($dbh, $fih);
	$this->cmpf($fvp);
} 

private function cmpf($p) {
	$fs = ['f', 'd'];
	foreach($fs as $suf) {
		$r[$suf] = file_get_contents($p . '_' . $suf);
	}
	
	$this->cmp($r['d'], $r['f']);
}

private function cmp($d, $f) {
	$a = get_defined_vars();
	foreach($a as $k => $raw) {
		$c[] = $h = self::getit($raw);
		echo($h . ' = ' . $k . "\n");
	}
	
	if ($c[0] === $c[1]) {
		echo('OK - GOALLLLLLLLLL!!!!!!'. "\n");
	}
	
}

public static function getit($s) {
	preg_match('/\s[0-9a-f]{32}/', $s, $ms);
	return trim($ms[0]);
}

private function di05($sz) {
	$szd = number_format($sz);
	echo("$szd bytes\n"); unset($szd, $nd);	
}
}
