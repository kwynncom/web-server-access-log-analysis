<?php

require_once('/opt/kwynn/kwutils.php');
$fn = '/var/kwynn/logs/a10K';
// $r = fopen($fn, 'r');       // forward   8302975721575955999
$r = popen("tac $fn", 'r');    // backwards 8302975721575955999

$fx = 0;

while ($l = fgets($r)) {

	$a = unpack('P*', $l);
	$lx = 0;
	foreach($a as $v) $lx ^= $v;
	$fx ^= $lx;
}

echo($fx . "\n");
