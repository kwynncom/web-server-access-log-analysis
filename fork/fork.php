<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class fork {
    
    const reallyDo = true;
    
    public static function dofork($childFunc, $startat, $endat) {
	
	$mcr = multi_core_ranges::get($startat, $endat);
		
	$cpun = $mcr['cpun'];
	$cpids = [];
	for($i=0; $i < $cpun; $i++) {
	    if (self::reallyDo) $pid = pcntl_fork();
	    if (!isset($pid) || $pid === 0) {
		call_user_func($childFunc, $mcr['ranges'][$i]['l'], $mcr['ranges'][$i]['h'], $i);
		exit(0);
	    }  
	    $cpids[] = $pid;
	}
	for($i=0; $i < $cpun; $i++) pcntl_waitpid($cpids[$i], $status);
    }
} // class
