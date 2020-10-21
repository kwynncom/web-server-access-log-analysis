<?php

require_once('/opt/kwynn/kwutils.php');

class fork {

    const maxcpus = 300; // AWS has a 96 core processor as of early 2020
    
    public static function getCPUCount() { return self::validCPUCount(shell_exec('grep -c processor /proc/cpuinfo'));   }
    
    public static function validCPUCount($nin) {
	$nin = trim($nin);
	kwas(is_numeric($nin), 'cpu count not a number');
	$nin = intval($nin);
	kwas($nin >= 1 && $nin <= self::maxcpus, 'invalid number of (hyper)threads / cores / cpus');
	return $nin;
    }
    
    
    public static function dofork($childFunc) {
	$cpun = 1;

	$cpids = [];
	for($i=0; $i < $cpun; $i++) {

	    $pid = pcntl_fork();

	    if ($pid === 0) {
		call_user_func($childFunc);
		exit(0);
	    }  

	    $cpids[] = $pid;
	}

	for($i=0; $i < $cpun; $i++) pcntl_waitpid($cpids[$i], $status);
    }
    
    public static function getRanges($totn, $sat, $cpuin = false) { 

	if ($cpuin) $cpun = self::validCPUCount($nin);
	else	    $cpun = self::getCPUCount();
	
	$rs = [];
	
	$ttd = $totn - $sat + 1;

	for ($i=0; $i < $cpun; $i++) {

	    if ($i === 0) $rs[$i]['l'] = $i + $sat;
	    if ($i < ($cpun - 1)) {
		$rs[$i]['h'] = self::getHigh($ttd, $cpun, $sat);
		$rs[$i + 1]['l'] = $rs[$i]['h'] + 1;    
	    } else $rs[$i]['h'] = $ttd + $sat - 1;

	    $l = $rs[$i]['l'];
	    $h = $rs[$i]['h'];
	}

	return $rs;
    }
    
    private static function getHigh($ttd, $cpun, $sat) {
	$try = intval(round(($ttd / $cpun) * ($i + 1))) + $sat;   	
	if ($try > $ttd) return $ttd;
	return $try;
    }
    
    public static function tests() {
	$ts = [
		[1, 1]
	    ];
	
	foreach($ts as $t) {
	    if (!isset($t[2])) $t[2] = false;
	    try {
		$res = self::getRanges($t[0], $t[1], $t[2]);
		$out = [];
		$out['in'] = $t;
		$out['out'] = $res;
		print_r($out);
	    } catch (Exception $ex) {
		$ignore = 2;
	    }
	}
    }
}

if (didCLICallMe(__FILE__)) fork::tests();