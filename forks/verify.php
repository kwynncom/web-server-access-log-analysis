<?php

class wsal_verify {
public function __construct ($db, $c, $f, $n, $ts, $sz) {
	$this->do10($db, $c, $f, $n, $ts, $sz);
	
} // func

private function do10($db, $c, $f, $n, $ts, $sz) {
	$pid = 0;
	// $pid = fork();
	
	$szd = number_format($sz);
	$nd  = number_format($n);
	echo("$nd lines, $szd bytes\n");
	
	if ($pid === 0) {
		$q = '';
		$q .= 'mongo wsal --quiet -eval ';
		$q .= <<<Q0024
"db.getCollection('$c').find({'ftsl1' : $ts}).sort({'fpp1' : 1}).limit($n).forEach(function(r) { print(r.line.trim()); });" | openssl md5
Q0024;

		$s = shell_exec($q);
		echo("db = " . $s);
		
	}
	
	if ($pid === 0) {
		$c = "openssl md5 $f";
		$r = shell_exec($c);
		echo($r);
		
	}
} // func
} // class
