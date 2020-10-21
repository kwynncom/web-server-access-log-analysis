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
    
    public static function getRanges($totn, $stat, $off = 1, $cpuin = false) { 

	kwas(is_numeric($totn) && is_numeric($stat), 'bad numbers 1 getRanges()');
	$totn = intval($totn); $stat = intval($stat); kwas($totn >= 0 && $stat >=0, 'bad numbers 2 getRanges()');
	kwas($stat <= $totn, 'bad 3 getRanges()');
	
	if ($cpuin) $cpun = self::validCPUCount($nin);
	else	    $cpun = self::getCPUCount();
	
	$rs = [];
	
	if ($totn === 0) $itd = 0;
	else		 $itd = $totn - $stat + 1;
	
	$h = -1; // just because the logic works

	for ($i=0; $i < $cpun; $i++) {

	    if ($h >= $itd) { $rs[$i]['l'] = $rs[$i]['h'] = false; continue; }
	    
	    if ($i === 0) {
		$l = $rs[$i]['l'] = self::getLow ($itd, $cpun, $stat, $i);
		$h = $rs[$i]['h'] = self::getHigh($itd, $cpun, $stat, $i, $l);
	    } else
	    if ($i < ($cpun - 1)) {
		$h = $rs[$i]['h'] = self::getHigh($itd, $cpun, $stat, $i, $l);
		$l = $rs[$i + 1]['l'] = self::getLow ($itd, $cpun, $stat, $i + 1, $h);

	    } else $h = $rs[$i]['h'] = self::getHigh($itd, $cpun, $stat, $i, $l);



	}

	return $rs;
    }
    
    private static function getLow($itd, $cpun, $stat, $i, $hm1 = false) {
	
	if ($itd === 0) return false;
	
	if ($i === 0) $r = $i   + $stat;	
	else if ($hm1 === false) return false;
	else          $r = $hm1 + 1;  
	
	if ($r > $itd) $r = false;
	
	return $r;
    }
    
    private static function getHigh($itd, $cpun, $stat, $i, $l) {
	
	if ($l === false) return false;
	
	if ($i < ($cpun - 1)) {
	    $try = intval(floor(($itd / $cpun) * ($i + 1))) /* + $stat*/;   	
	} else $try = $itd + $stat - 1;
	
	if ($try > $itd) return $itd;
	
	if ($try === 0) return $l;
	
	return $try;
    }
    
    public static function tests() {
	$ts = [
		// [0, 0],
		// [12,1],
		[ 6,1]
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