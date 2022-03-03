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

	if (!$a) return 0;
	if ($a['fpp1'] >= $sz) {
		echo("file already loaded\n");
		return false;
	}

	return ['bpr' => $a['fpp1'], 'fts1' => $ts, 'sz' => $sz];
}