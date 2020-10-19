<?php

function wsal_fork($ltodo, $cpus, $start) {
    
    kwas($cpus < 200, 'probably too many cpus and wrong param');
    
    if (0) {
	    $rs = getRanges($cpus, $ltodo, $start);
	    exit(0);
    }
    
    for($i=0; $i < $cpus; $i++) {
    

        $pid = pcntl_fork();
    
        if ($pid === 0) {
	    $rs = getRanges($cpus, $ltodo, $start);
	    return $rs[$i];
	}
    }
    
    return $pid;
}

function getRanges($cpus, $lines, $off) { 

    $ranges = [];
    
    for ($i=0; $i < $cpus; $i++) {
	if ($i === 0) $ranges[$i]['l'] = $i + $off;
	if ($i < ($cpus - 1)) {
	    $ranges[$i]['h'] = intval(round(($lines / $cpus) * ($i + 1))) + $off;   
	    $ranges[$i + 1]['l'] = $ranges[$i]['h'] + 1;    
	} else $ranges[$i]['h'] = $lines + $off - 1;


	$l = $ranges[$i]['l'];
	$h = $ranges[$i]['h'];
    }
    
    return $ranges;
}