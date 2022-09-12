<?php

function getLLFile($ov = '') { // get local, (to be synced with) live file
	if ($ov) $p = $ov;
	else	 $p = trim(file_get_contents('/var/kwynn/logpath.txt'));
	$cmdf = 'find ' . $p . ' -type f -printf "%T+\t%p\n" | sort -r | grep access | grep log | head -n 1 | awk \'{print $2}\'';
    $l = trim(shell_exec($cmdf));
	return $l;
}
