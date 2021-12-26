<?php

require_once('/opt/kwynn/kwutils.php');

function myips_manual_20() {
	$ret = [];
	$f = '/var/kwynn/myips.txt';
	if (!is_readable($f)) return $ret;
	$t = file_get_contents($f);
	$a = explode("\n", $t);
	foreach($a as $r) {
		if (!trim($r)) continue;
		kwas(preg_match('/([0-9A-Fa-f:\.]{7,39})\s+(\S+.+$)/', $r, $ms), 'bad ip agent match myips20');
		$ip = $ms[1];
		$as = trim($ms[2]);
		kwas($as && is_string($as) && strlen($as) >= 1, 'bad string length us myips manual 20');
		$ta['agent'] = $as;
		$ret[$ip][$as] = true;
		
		continue;
		
	}
	
	return $ret;
	
	
}

if (didCLICallMe(__FILE__)) myips_manual_20();