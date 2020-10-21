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
	
	$h = 0; // just because the logic works
	$l = 0;

	for ($i=0; $i < $cpun; $i++) {
	    
	    if (isset($lf) && $lf === false) { $rs[$i]['l'] = $rs[$i]['h'] = false; continue; }

	    if ($i === 0) { self::set($l, $rs, 'l', $i, $i + $stat, $stat, $totn);  }
	    if ($i < ($cpun - 1)) {
		$h = intval(round(($itd / $cpun) * ($i + 1)));   
		if ($h < $l) $h = $l;
		$l1 = $h + 1;
	    } else $h = $itd + $stat - 1;

	    $hf = self::set($h, $rs, 'h', $i    , $h , $stat, $totn);
	    $lf = self::set($l, $rs, 'l', $i + 1, $l1, $stat, $totn);
   
	}

	return $rs;
    }
    
    private static function set(&$lhr, &$a, $lhk, $i, $to, $stat, $totn) {
	if ($to > $totn) $to = false;
	else $to = $to;
	$lhr = $to;
	$a[$i][$lhk] = $to;
	return $to;
    }
    
    public static function tests() {
	$ts = [
		// [0, 0],
		// [12,1],
		// [ 6,1],
	       // [1,0]
		[200,0]
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