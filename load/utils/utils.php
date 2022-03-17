<?php

function getFSz($f) {
	kwas(is_readable($f), "$f file not readable");
	$sz =   filesize($f);	
	return $sz;
}

function wsal_getL1AndCk($fname, $sz = false, $dbname = false) {

	$h = fopen($fname, 'r');
	$l = fgets($h);
	fclose($h);
	$ts = wsal_parse_2022_010::parse($l, true);

	if ($sz === false) return $ts;

	if ($sz === true) $sz = getFSz($fname);

	$q = "db.getCollection('lines').find({'ftsl1' : $ts }).sort({'fpp1' : -1}).limit(1)";
	$a = dbqcl::q($dbname, $q);

	if (!$a) return ['bpr' => 0, 'ftsl1' => $ts, 'sz' => $sz];
	if ($a['fpp1'] >= $sz) {
		echo("file already loaded\n");
		return false;
	}

	return ['bpr' => $a['fpp1'], 'ftsl1' => $ts, 'sz' => $sz];
}

/* 
$_id
$line
$fp0
$fpp1
$len
$ftsl1

 * 
 */

function wsal_lineAF(int $ftsl1, int $fp0, int $fpp1, string $line, int $len = 0, string $_id = '') {
	if (!$len) $len = strlen($line);
	if (!$_id) unset($_id);
	return get_defined_vars();
}