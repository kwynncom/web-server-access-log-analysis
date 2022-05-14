<?php

// tail -n 200 /var/log/apache2/access.log | 

require_once(__DIR__ . '/bots/bots.php');
require_once(__DIR__ . '/load/utils/parse.php');

$r = fopen('php://stdin', 'r');
while ($l = fgets($r)) {
	$a = wsal_parse_2022_010::parse($l);
	if (wsal_bots::isBot($a['agent'])) continue;
	echo($l);
}
