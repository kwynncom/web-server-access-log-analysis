<?php

require_once('db.php');
require_once('file.php');

class wsal_verify {
public function __construct(  $db, $c, $f, $ts, $sz, $bpr, $epr, $isl) {
	$dbo = new wsal_verify_db($db, $c,     $ts,      $bpr, $epr, $isl);
	$this->di05($sz);
	$fo = new wsal_verify_fi($f, $isl, $dbo->getCounts());
	$dbh = $dbo->getHash();
	$fih = $fo->getHash();
	$this->cmp($dbh, $fih);
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

public function getit($s) {
	preg_match('/\s[0-9a-f]{32}/', $s, $ms);
	return trim($ms[0]);
}

private function di05($sz) {
	$szd = number_format($sz);
	echo("$szd bytes\n"); unset($szd, $nd);	
}
}
