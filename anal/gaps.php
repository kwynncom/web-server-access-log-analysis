<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/../utils/getLocalFile.php');
require_once(__DIR__ . '/../utils/parse.php');

function doit() {
	$h = fopen(getLLFile(), 'r');
	$b = [];
	$i = 0;
	$max = -1;
	while($l = fgets($h)) {
		$mi = $i % 2;
		$buf[$mi] = wsal_parse::parse($l, true);
		if (!isset($buf[1])) {
			$i++;
			continue;
		}
		
		$a = $buf[	  $mi];
		$b = $buf[1 - $mi];
		$i++;
		
		
		$d = abs($a - $b);
		if ($d > $max) { 
			echo($l);
			echo($d . "\n");
			$max = $d;
		}
		
	}
	
	echo($i . ' === $i' . "\n");
	
	return;
}

doit();