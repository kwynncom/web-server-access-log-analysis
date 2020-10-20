<?php

require_once('dao.php');
require_once('anal10.php');

class wsla_agent_p30 extends dao_wsal_anal {
    function __construct() {
	parent::__construct();
	$this->load();
	$this->p10();
	
    }
    
    private static function sr($se, $r, &$sub) { $sub = str_replace($se, $r, $sub);  }
    
    private function p10() {
	
	static $i = 0;
	
	$a = $this->biga;
	foreach($a as $r) {
	    $p20 = wsal_anal10::agent20($r['agentp10']);
	    
	    // "(X11; Datanyze; Linux x86_64) Chrome/65.0.3325.181 Safari/537.36"
	    $c10r = preg_match('/(Chrome\/\d+)(\.[\d+\.]*)/', $p20, $c10m);
	    $s10r = preg_match('/Safari\/[\d+\.]+/', $p20, $s10m);
	    
	    $p30 = $p20;
	    
	    if ($c10r && $s10r) {
		$p30 =      str_replace($s10m[0], ''      , $p30);
		$p30 = trim(str_replace($c10m[0], $c10m[1], $p30));
	    }
	    
	    $p30 = preg_replace('/ Gecko\/\d+/ ', '', $p30);
	    $p30 = str_replace('Windows', 'Win', $p30);
	    $p30 = str_replace('Linux; Android', 'Android', $p30);
	    self::sr('SamsungBrowser', 'SamBr', $p30);
	    self::sr('SAMSUNG SM', 'SAM SM', $p30);	    
	    self::sr('; Win64; x64;', ' x64', $p30);
	    self::sr('(','', $p30);
	    self::sr(')','', $p30);
	    
	    if (strpos($p30, 'SamBr') && strpos($p30, ' Mobile')) self::sr(' Mobile', '', $p30);
	    
	    
	    if (++$i >= 4 && $i <= 30) {
		echo $p30 . "\n";
	    }
	    
	    continue;
	    
	}
    }
    
    private function load() {
	$pr = ['projection' => ['_id' => 0, 'agentp10' => 1]];
	
	$q['ts']  = ['$gte' => strtotime('2020-10-15')];
	$q['bot'] = false;
	
	$res = $this->a10coll->find($q, $pr)->toArray();
	$this->biga = $res;
	
    }
    
}

if (didCLICallMe(__FILE__)) {
    new wsla_agent_p30();
    
}