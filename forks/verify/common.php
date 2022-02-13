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
	foreach($a as $k => $h) {
		
	}
}

private function di05($sz) {
	$szd = number_format($sz);
	echo("$szd bytes\n"); unset($szd, $nd);	
}
}
