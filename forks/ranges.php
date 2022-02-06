<?php

require_once('/opt/kwynn/kwutils.php');

class multi_core_ranges {
    
    const maxcpus = 300; // AWS has a 96 core processor as of early 2020
    
    public static function getCPUCount() { return self::validCPUCount(shell_exec('grep -c processor /proc/cpuinfo'));   }
    
    public static function validCPUCount($nin) {
	$nin = trim($nin);
	kwas(is_numeric($nin), 'cpu count not a number');
	$nin = intval($nin);
	kwas($nin >= 1 && $nin <= self::maxcpus, 'invalid number of (hyper)threads / cores / cpus');
	return $nin;
    }
    
    public static function get($stat, $endat, $cpuin = false) { 

	kwas(is_numeric($endat) && is_numeric($stat), 'bad numbers 1 getRanges()');
	$endat = intval($endat); $stat = intval($stat); kwas($endat >= 0 && $stat >=0, 'bad numbers 2 getRanges()');
	
	if ($cpuin) $cpun = self::validCPUCount($cpuin);
	else	    $cpun = self::getCPUCount();
	
	$rs = [];
	
	if ($endat === 0) $itd = 0;
	else		 $itd = $endat - $stat + 1;
	
	$h = true; // just because the logic works
	$l = true;

	for ($i=0; $i < $cpun; $i++) {
	    
	    if ($l === false || $h === false) { $rs[$i]['l'] = $rs[$i]['h'] = false; continue; }
	    
	    if ($i === 0) self::set($l, $rs, 'l', $i, $i + $stat, $stat, $endat);
	    else          self::set($l, $rs, 'l', $i, $h + 1, $stat, $endat);
	    if ($i < $cpun - 1) {
		$h = intval(round(($itd / $cpun) * ($i + 1))) + $stat;   
	    } else $h = $itd + $stat - 1;

	    self::set($h, $rs, 'h', $i    , $h , $stat, $endat, $l, $h);

   
	}

	return $rs;
    }
    
    private static function set(&$lhr, &$a, $lhk, $i, $to, $stat, $endat, $l = false, $h = false) {
	if ($endat === 0) return self::set20($lhr, $a, $lhk, false, $i);
        if ($to > $endat) $to = false;
        else $to = $to;
	
	if ($lhk === 'h' && $l === false) return self::set20($lhr, $a, $lhk, false, $i);
	
	if ($h < $l && $lhk === 'h') $to = $l; 
	
	$lhr = $to;
	$a[$i][$lhk] = $to;
	return $to;
    }
    
    private static function set20(&$lhr, &$a, $lhk, $to, $i) {
	$lhr = $a[$i][$lhk] = $to;
	
    }
    
    public static function tests() {
	$ts = [
		[1592696603, 1603313775],
		[1, 284717],
		[0, 0],
		[1, 1],
		[1, 2, 4],
	    	[0, 2, 1],
	    	[1, 2, 1],
		[1, 0],
		[1, 4, 6],
		[12,1],
		[1, 6],
	        [0, 1],
		[0, 200],
		
	    ];
	

	$max = count($ts) - 1;
	
	for ($i=1; $i <= 1; $i++) {
	$t = $ts[$i];
	if (!isset($t[2])) $t[2] = 12;
	try {
	    $res = self::get($t[0], $t[1], $t[2]);
	    $out = [];
	    $out['in'] = $t;
	    $out['out'] = $res;
	    print_r($out);
	} catch (Exception $ex) {
	    throw $ex;
	}
	}
    } // func

}

if (didCLICallMe(__FILE__)) multi_core_ranges::tests();
