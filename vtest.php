<?php

require_once('/opt/kwynn/kwutils.php');
 
$fx = 0;
while ($l = fgets(STDIN)) {
	$a = unpack('P*', $l);
	$lx = 0;
	foreach($a as $v) $lx ^= $v;
	$fx ^= $lx;
}

echo($fx . "\n");
