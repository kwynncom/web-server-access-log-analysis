<?php

function sex($h, $n) { return strpos($h, $n) !== false; }
function sr($se, $r, &$sub) { $sub = str_replace($se, $r, $sub); return $sub; }
function pr($ser, $r, &$sub) { $sub = preg_replace('/' . $ser . '/', $r, $sub); return $sub; }

class wsal_agent {
    public static function filter($ain) {
	return self::f10($ain);
    }
    
    private static function win10(&$s) {
	$s = str_replace(';', '', $s);
	pr('Windows NT ([\d\.]+) Win64 x64', 'Windows $1', $s);	
    }
    
    public static function f10($sin) {
	
	$s = $sin;

	self::win10($s);
	
	self::f25($s); self::f20($s);
	
	$s = str_replace('Mozilla/5.0', '', $s);
	$s = str_replace('(X11; Ubuntu; Linux ', 'Ubuntu Linux', $s);
	$s = preg_replace('/ Gecko\/\d+/', '', $s);
	$s = str_replace('x86_64', '', $s);
	preg_match('/(rv\:(\d+\.\d+)\)) (Firefox\/(\d+\.\d+))/', $s, $m);
	if (isset($m[4]) && $m[4] === $m[2]) $s = str_replace($m[0], $m[3], $s);
	// $s = str_replace(';', ' ', $s);
	$s = preg_replace('/\s+/', ' ', $s);
	
	if (sex($s, 'Linux') && sex($s, 'Android'))  $s = str_replace('Linux', '', $s);
	if (sex($s, 'Chrome') && sex($s, 'Safari'))  $s = preg_replace('/Safari\/[\d\.]+/', '', $s);
	if (sex($s, 'Mobile') && sex($s, 'Android')) $s = str_replace('Mobile', '', $s);	

	foreach(['Chrome', 'Firefox', 'Safari'] as $v) $s = preg_replace('/(' . $v . '\/\d+)[\d\.]*/', '$1', $s);
	
	pr('Macintosh\;* Intel Mac OS X', 'OSX', $s);
	
	// OSX 10_15_6 Version/13.1.2 Safari/605
	if (sex($s, 'OSX')) pr('Version\/[\d\.]+', '', $s);
	
	$s = preg_replace('/AppleWebKit\/[\d\.]+/', '', $s);
	$s =  str_replace('KHTML, like Gecko'    , '', $s);

	
	$s = str_replace('Linux X11', 'Linux', $s);
	
	
	$s = str_replace(';', ' ', $s);
	$s = preg_replace('/[\(\)]/', '', $s);
	$s = trim(preg_replace('/\s+/', ' ', $s));
	
	
	return $s;
    }
    
    private static function f25(&$s) {
	// Mozilla/5.0 iPhone [FBAN/FBIOS;FBDV/iPhone10,5;FBMD/iPhone;FBSN/iOS;FBSV/14.0.1;FBSS/3;FBID/phone;FBLC/en_US;FBOP/5]	
	if (!preg_match('/\[[^\[]*FBSV\/([\d+\.]+)[^\]]*\]/', $s, $m)) return;
	sr($m[0], 'v' . $m[1], $s);
	return;
    }
    
    private static function f20(&$s) {
	
	// iPad; CPU OS 13_7 like Mac OS X GSA/128.0.335051718 Mobile/15E148 Safari/604
	
	if (strpos($s, 'iPhone') === false &&
	    strpos($s, 'iPad'  ) === false    ) return;
	$ignore = 233;

	$s = str_replace('iPad; CPU OS ', 'iPad ', $s);	
	$s = str_replace('iPhone; CPU iPhone OS ', 'iPhone ', $s);
	$s = str_replace(' like Mac OS X', ' ', $s);
	$s = str_replace('Version/', 'v', $s);	
	// $s = preg_replace('/\d+_[\d_]+/', '', $s);
	$s = preg_replace('/Mobile\/[\dA-F]+/', '', $s);	
//	$s = preg_replace('/Safari\/[\d\.]+/', '', $s);	
	$s = preg_replace('/AppleWebKit\/[\d\.]+/', '', $s);	
	self::f25($s);
	
	pr('v[\d\.]+', '', $s);
	
	$s = preg_replace('/\s+/', ' ', $s);
	$s = trim($s);
	
	// iPhone; CPU iPhone OS 14_0_1Version/14.0 Mobile/15E148 Safari/604.1

	
    }
}