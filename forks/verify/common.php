<?php

require_once('db.php');
require_once('file.php');

class wsal_verify {
public function __construct		 ($db, $c, $f, $ts, $sz, $bpr, $epr, $isl) {
		$dbo = new wsal_verify_db($db, $c,     $ts,      $bpr, $epr, $isl);
		$this->di05($sz);
		$fo = new wsal_verify_fi($f, $isl, $dbo->getCounts());
		$dbo->getHash();
		$fo->getHash();
	} 

	private function di05($sz) {
		$szd = number_format($sz);
		echo("$szd bytes\n"); unset($szd, $nd);	
	}
}
