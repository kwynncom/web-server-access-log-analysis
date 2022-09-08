<?php

require_once('/opt/kwynn/kwutils.php');

function getcur($h, $ty = '') {
	
	$ts = 0;
	$b = '';

	do {
		
		if ($ty === 'login') {
			 if (isset($b[400])) break;
			 usleep(100000);
		}
		else usleep(50000);
		
		
		while (is_resource($h) && !feof($h)) {
			$ra = [$h]; $na = [];
			if (!stream_select($ra, $na, $na, 0, 10000)) break;
			$c = fgets($h);
			$b  .= $c;
		} 
		
	} while($ts++ < 200);
	
	return $b;
}


	if (1) {

	$cmd = 'goa';

	$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
	$io;
	$inpr = proc_open($cmd, $pdnonce, $io); unset($pdnonce);
	$ouh  = $io[0];
	$inh  = $io[1]; unset($io);
	echo(getcur($inh, 'login'));

	// fwrite($ouh, 'cd /var/log/apache2' . "\n");
	// getcur($inh);
	// fwrite($ouh, 'ls -l' . "\n");
	// echo(getcur($inh));
	fwrite($ouh, 'exit' . "\n");
	fclose($inh); 
	fclose($ouh);
	proc_close($inpr);
}