<?php

require_once('/opt/kwynn/kwutils.php');

$i = 0;
$fx = 0;
$lx = 0;
$c64 = 0;

while (($lnbuf = fgets(STDIN)) != NULL) {
	$lx = 0;
	$i  = 0;
	do {
		if (!isset($lnbuf[$i])) break;
		$c64 = ord($lnbuf[$i]);
		$lx ^= $c64 << (8 * ($i % 8));
		$i++;
	} while($i < 8010);
	$fx ^= $lx;
}

printf("%d\n", $fx);
