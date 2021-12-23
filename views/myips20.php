<?php

require_once('/opt/kwynn/kwutils.php');

function myips_manual_20() {
	$t = file_get_contents('/var/kwynn/myips.txt');
	$a = explode("\n", $t);
	$ret = [];
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