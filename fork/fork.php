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
    
    public static function getRanges($stat, $totn, $off = 1, $cpuin = false) { 

	kwas(is_numeric($totn) && is_numeric($stat), 'bad numbers 1 getRanges()');
	$totn = intval($totn); $stat = intval($stat); kwas($totn >= 0 && $stat >=0, 'bad numbers 2 getRanges()');
	// kwas($stat <= $totn, 'bad 3 getRanges()');
	
	if ($cpuin) $cpun = self::validCPUCount($nin);
	else	    $cpun = self::getCPUCount();
	
	$rs = [];
	
	if ($totn === 0) $itd = 0;
	else		 $itd = $totn - $stat + 1;
	
	$h = true; // just because the logic works
	$l = true;

	for ($i=0; $i < $cpun; $i++) {
	    
	    if ($l === false || $h === false) { $rs[$i]['l'] = $rs[$i]['h'] = false; continue; }
	    
	    if ($i === 0) self::set($l, $rs, 'l', $i, $i + $stat, $stat, $totn);
	    else          self::set($l, $rs, 'l', $i, $h + 1, $stat, $totn);
	    if ($i < $cpun - 1) {
		$h = intval(round(($itd / $cpun) * ($i + 1)));   
	    } else $h = $itd + $stat - 1;

	    self::set($h, $rs, 'h', $i    , $h , $stat, $totn, $l, $h);

   
	}

	return $rs;
    }
    
    private static function set(&$lhr, &$a, $lhk, $i, $to, $stat, $totn, $l = false, $h = false) {
	if ($totn === 0) return self::set20($lhr, $a, $lhk, false, $i);
        if ($to > $totn) $to = false;
        else $to = $to;
	
	if ($lhk === 'h' && $l === false) return self::set20($lhr, $a, $lhk, false, $i);
	
	if ($h < $l && $lhk === 'h') $to = $l; 
	
	$lhr = $to;
	$a[$i][$lhk] = $to;
	return $to;
    }
    
    public static function set20(&$lhr, &$a, $lhk, $to, $i) {
	$lhr = $a[$i][$lhk] = $to;
	
    }
    
    public static function tests() {
	$ts = [
		[0, 0],
		[1, 1],
		[1, 2, 4],
	    	[1, 2, 1],
		[1, 0],
		[1, 4, 6],
		[12,1],
		[1, 6],
	        [0, 1],
		[0, 200],
		
	    ];
	

	$max = count($ts) - 1;
	
	for ($i=3; $i <= 3; $i++) {
	$t = $ts[$i];
	if (!isset($t[2])) $t[2] = false;
	try {
	    $res = self::getRanges($t[0], $t[1], $t[2]);
	    $out = [];
	    $out['in'] = $t;
	    $out['out'] = $res;
	    print_r($out);
	} catch (Exception $ex) {
	    throw $ex;
	}
	}
    } // func
} // class

if (didCLICallMe(__FILE__)) fork::tests();