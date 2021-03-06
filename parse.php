<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_parse {
    
    public static function parse($lin, $tsonly = false) {
	$a10 = self::p10($lin, $tsonly);
	if ($tsonly) return $a10;
	$a20 = self::p20($a10);
	return $a20;
    }
    
    private static function p20($a) {
	$cv  = $a['htCmdAndV'];

	$url = false;

	if ($cv !== '-') {
	    $rev = strrev($cv);
	    preg_match('/([\S]*)/', $rev, $mas);
	    $cmd = strrev(substr($rev, strlen($mas[1])));
	    $v = strrev($mas[1]);
	    $ext = trim(pathinfo($cmd, PATHINFO_EXTENSION));
	    preg_match('/([\S]+) ([\S]+)/', $rev, $mas);
	    if (isset($mas[2])) $url = strrev($mas[2]);
	} else {
	    $cmd = '-';
	    $v   = false;
	    $ext = '';
	}

	$r['cmd'] = $cmd;
	$r['htv'] = $v;
	$r['ext'] = $ext;
	$r['url'] = $url;

	return array_merge($a, $r);
    }
    
    private static function us($usl) {
	preg_match('/^(\d+) /', $usl, $ms); kwas(isset($ms[1]), 'either HTTP command or microseconds');
	$uslen = strlen($ms[0]);
	$usec  = intval($ms[1]);
	unset($usl, $ms);
	$vars = get_defined_vars(); 
	return $vars;
    }

    private static function p10($wl, $tsonly) {

	$wl = trim($wl); kwas($wl, 'there should not be any blank lines - wsal parse');

	$lda = []; // line data array

	$tln = $wl;    

	$ipre = '/[0-9A-Fa-f:\.]+/'; // IP address regular expression

	kwas(preg_match($ipre, $tln, $ipmatches) === 1, 'ip regex fail');
	$lda['ip'] = $ipmatches[0]; 

	$tln = substr($tln, strlen($lda['ip']) + 1);

	if (substr($tln, 0, 4) === '- - ') { $tln = substr($tln, 4); }
	else die('user - - failed');

	$dateStr = substr($tln, 1, 26); 

	$ts = strtotime($dateStr);
	
	if ($tsonly) return $ts;

	$lda['dates'] = $dateStr;
	$lda['ts']   = $ts;
	$lda['line'] = $wl;

	$tln = substr($tln, 29);  
	
	if ($tln[0] !== '"') {
	    extract(self::us($tln));
	    $tln = substr($tln, $uslen); kwas($tln[0] === '"', 'quotes expected - wsal parse');
	} else $usec = 0;

	$lda['us'] = $usec;
	$lda['tsus'] = $ts * M_MILLION + $usec; unset($usec, $uslen);
	
	$tln = substr($tln, 1);
	$endc = strpos($tln, '" ');

	$httpCmd = trim(substr($tln, 0, $endc)); 
	$lda['htCmdAndV'] = $httpCmd; 
	$tln = substr($tln, strlen($httpCmd) + 2);

	preg_match('/^(\d+) ([\w-]+)/', $tln, $matchesCodeAndLen); 
	try {
	    kwas(isset($matchesCodeAndLen[2]), 'HTTP code and length fail');
	} catch(Exception $ex) {
	    throw $ex;
	}

	$codeiv = intval($matchesCodeAndLen[1]);
	try {
	kwas($codeiv, 'ht code not int');
	} catch(Exception $ex) {
	    $blah = 1;
	    throw $ex;
	}

	$lda['httpcode']     = $codeiv;
	
	if (!is_numeric($matchesCodeAndLen[2])) {
	    $len = 0;
	}
	else {
	    $len = intval($matchesCodeAndLen[2]); 
	}
	
	$lda['len']          = $len; unset($len);
	$tln = substr($tln, strlen($matchesCodeAndLen[0])); 


	preg_match('/\"([^\"]*)\" /', $tln, $matchesEndOfLine); kwas(isset($matchesEndOfLine[1]), 'referrer match fail');

	$lda['ref'] = $matchesEndOfLine[1]; 
	$tln = substr($tln, strlen($matchesEndOfLine[1]) + 4); 

	kwas(isset($tln[1]), 'agent too short');

	$tln = substr($tln, 1, strlen($tln[1]) - 2);
	$lda['agent'] = trim($tln);

	return $lda;
    }
} // class