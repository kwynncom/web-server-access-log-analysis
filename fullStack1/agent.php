<?php

require_once('dao.php');
require_once('anal10.php');

class wsla_agent_p30 extends dao_wsal_anal {
    function __construct() {
	parent::__construct();
	$this->load();
	$this->test10();
	
    }
    
    private static function f25(&$s) {
	// Mozilla/5.0 iPhone [FBAN/FBIOS;FBDV/iPhone10,5;FBMD/iPhone;FBSN/iOS;FBSV/14.0.1;FBSS/3;FBID/phone;FBLC/en_US;FBOP/5]	
	if (!preg_match('/\[[^\[]*FBSV\/([\d+\.]+)[^\]]*\]/', $s, $m)) return;
	self::sr($m[0], 'v' . $m[1], $s);
	return;
    }
    
    private static function f20(&$s) {
	
	if (strpos($s, 'iPhone') === false) return;
	$ignore = 233;
	
	$s = str_replace('iPhone; CPU iPhone OS ', 'iPhone ', $s);
	$s = str_replace(' like Mac OS X ', ' ', $s);
	$s = str_replace('Version/', 'v', $s);	
	$s = preg_replace('/\d+_[\d_]+/', '', $s);
	$s = preg_replace('/Mobile\/[\dA-F]+/', '', $s);	
//	$s = preg_replace('/Safari\/[\d\.]+/', '', $s);	
	$s = preg_replace('/AppleWebKit\/[\d\.]+/', '', $s);	
	self::f25($s);
	$s = preg_replace('/\s+/', ' ', $s);
	$s = trim($s);
	
	// iPhone; CPU iPhone OS 14_0_1Version/14.0 Mobile/15E148 Safari/604.1

	
    }
    
    private static function sr($se, $r, &$sub) { $sub = str_replace($se, $r, $sub);  }
    
    public static function get($p30) {
	$p30 = wsal_anal10::agent20($p30);
	$c10r = preg_match('/(Chrome\/\d+)(\.[\d+\.]*)/', $p30, $c10m);
	$s10r = preg_match('/Safari\/[\d+\.]+/', $p30, $s10m);

	if ($c10r && $s10r) {
	    $p30 =      str_replace($s10m[0], ''      , $p30);
	    $p30 = trim(str_replace($c10m[0], $c10m[1], $p30));
	}

	$p30 = preg_replace('/ Gecko\/\d+/ ', '', $p30);
	$p30 = str_replace('Windows', 'Win', $p30);
	$p30 = str_replace('Linux; Android', 'Android', $p30);
	self::sr('SamsungBrowser', 'SamBr', $p30);
	self::sr('SAMSUNG SM', 'SAM SM', $p30);	    
	self::sr('; Win64; x64', ' x64', $p30);
	self::sr('(','', $p30);
	self::sr(')','', $p30);
	if (strpos($p30, 'SamBr') && strpos($p30, ' Mobile')) self::sr(' Mobile', '', $p30);
	self::sr('Macintosh; Intel Mac OS X', 'OSX', $p30);
	// Linux x86_64
	self::sr('Linux x86_64', 'Linux x64', $p30);
	
	if ($s10r && strpos($p30, 'AppleWebKit')) {
	    $p30 = preg_replace('/AppleWebKit\/[\d\.]+/', '', $p30);
	}	
	
	$p30 = trim(preg_replace('/\s+/', ' ', $p30));
	
	self::f20($p30);
	
	return $p30;
    }
    
    private function test10() {
	
	static $i = 0;
	
	$a = $this->biga;
	foreach($a as $r) {
	    $p30 = self::get($r['agent']);


	    
	    
	    if (++$i >= 4 && $i <= 200) {
		echo $p30 . "\n";
	    }
	    
	    continue;
	    
	}
    }
    
    private function load() {
	$pr = ['projection' => ['_id' => 0, 'agent' => 1]];
	
	$q['ts']  = ['$gte' => strtotime('2020-10-15')];
	$q['bot'] = false;
	
	$res = $this->a10coll->find($q, $pr)->toArray();
	$this->biga = $res;
	
    }
    
}

if (didCLICallMe(__FILE__)) {
    new wsla_agent_p30();
    
}