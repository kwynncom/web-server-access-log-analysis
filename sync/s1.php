<?php

require_once('/opt/kwynn/kwutils.php');

function getcur($h) {

	static $ku   =    "ubuntu@ip-";
	// static $szku = /*  1 2345678901 */ 11;
	$b = '';
	$ts = 0;
	
//	 kwas(stream_set_timeout($h, 0, 5000), 'cannot set timeout');

	do {
		usleep(1000);
		while (is_resource($h) && !feof($h)) {
	    
			$ra = [$h]; $na = [];
			if (!stream_select($ra, $na, $na, 0, 5000)) {
				// trigger_error('Timeout');
				break;
			}

			$c = fgetc($h);

			$b .= $c;
			// echo($c);
			if ($c === "\n") {
				echo($b);
				if (strpos($b, 'upgradable')) {
					$ignore =2;
					
				}
				
				$b = '';
			}
			
			if (!isset($b[2])) continue;
			if (strpos($b, $ku) === false) continue;
			$l = strlen($b);
			$ck = substr($b, $l - 2, 2);
			if ($ck === '$ ') return $b;
		} 
		
	} while($ts++ < 1000);

	return '';
}


	if (1) {

	$cmd = 'goa';

	$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
	$io;
	$inpr = proc_open($cmd, $pdnonce, $io); unset($pdnonce);
	$ouh  = $io[0];
	$inh  = $io[1]; unset($io);
	echo(getcur($inh));

	fwrite($ouh, 'cd /var/log/apache2' . "\n");
	getcur($inh);
	fwrite($ouh, 'ls -l' . "\n");
	echo(getcur($inh));
	fclose($ouh);
	fclose($inh); proc_close($inpr);
	}