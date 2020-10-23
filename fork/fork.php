<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class fork {
    
    const reallyFork = true;
    
    public static function dofork($childFunc, $startat, $endat, $reallyForkIn = true) {
	
	$reallyFork = $reallyForkIn && self::reallyFork;
	
	$mcr = multi_core_ranges::get($startat, $endat, $reallyFork ? false : 1);
		
	$cpun = $mcr['cpun'];
	$cpids = [];
	for($i=0; $i < $cpun; $i++) {
	    $pid = -1;
	    if ($reallyFork) $pid = pcntl_fork();
	    if ($pid === 0 || !$reallyFork) {
		call_user_func($childFunc, $mcr['ranges'][$i]['l'], $mcr['ranges'][$i]['h'], $i);
		exit(0);
	    }  
	    $cpids[] = $pid;
	}
	for($i=0; $i < $cpun; $i++) pcntl_waitpid($cpids[$i], $status);
    }
} // class
