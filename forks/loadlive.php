<?php

// BETTER YET, use sshfs as documented in my sysadmin examples GitHub repo


// 0 is stdin, 1 is stdout, while 2 is stderr. 
	$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
	$io;
	
	$inpr = proc_open('goa', $pdnonce, $io); unset($pdnonce);
	$ouh  = $io[0];
	$inh  = $io[1];
	
	// dd skip=446519713 bs=1 if=a400M
	fwrite($ouh, 'ls' . "\n");
	fwrite($ouh, 'exit' . "\n");
	
	while ($l = fgets($inh)) {
		echo($l);
	}

	proc_close($inpr);
