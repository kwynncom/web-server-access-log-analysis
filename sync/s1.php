<?php

require_once('/opt/kwynn/kwutils.php');

function getcmd($h, $cf = null) {
	
	$b = '';
	$ts = 0;

	do {
		while (is_resource($h) && !feof($h)) {
			$ra = [$h]; $ignorea1 = $ignorea2 = [];
			if (!stream_select($ra, $ignorea1, $ignorea2, 0, 10000)) break;
			$c = fgets($h);
			$b  .= $c;
		} 
		
		if ($cf && $cf($b)) break;
		
	} while($ts++ < 500);
	
	return $b;
}


	if (1) {

	$cmd = 'goa';

	$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
	$io;
	$inpr = proc_open($cmd, $pdnonce, $io); unset($pdnonce);
	$ouh  = $io[0];
	$inh  = $io[1]; unset($io);
	
	echo(getcmd($inh, function($b) { return isset($b[400]); }));

	// fwrite($ouh, 'cd /var/log/apache2' . "\n");
	// echo(getcmd($inh));
	// fwrite($ouh, 'ls -l' . "\n");
	// echo(getcmd($inh));
	fwrite($ouh, 'exit' . "\n");
	fclose($inh); 
	fclose($ouh);
	proc_close($inpr);
}