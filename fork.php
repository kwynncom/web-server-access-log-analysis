<?php

function wsal_fork($ltodo) {
    
    $cpus = 1;
    // $pid  = 1; // just because the logic works
    
    for($i=0; $i < $cpus; $i++) {
    
	// if ($pid) 
	    $pid = pcntl_fork();
    
        if ($pid === 0) {
	    $rs = getRanges($cpus, $ltodo);
	    return $rs[$i];
	}
    }
    
    return $pid;
}

function getRanges($cpus, $lines) { 

    $ranges = [];
    
    for ($i=0; $i < $cpus; $i++) {
	if ($i === 0) $ranges[$i]['l'] = $i + 1;
	if ($i < ($cpus - 1)) {
	    $ranges[$i]['h'] = intval(round(($lines / $cpus) * ($i + 1)));   
	    $ranges[$i + 1]['l'] = $ranges[$i]['h'] + 1;    
	} else $ranges[$i]['h'] = $lines;


	$l = $ranges[$i]['l'];
	$h = $ranges[$i]['h'];
    }
    
    return $ranges;
}