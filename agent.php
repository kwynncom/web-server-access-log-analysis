<?php

// require_once('/opt/kwynn/kwutils.php'); // needed for "did CLI call me?"
require_once('dao.php');

class wsla_agent_p30  extends dao_wsal {
    function __construct($sort = 'asc') {
	$this->sortDir = $sort;
	parent::__construct(self::dbName, __FILE__);
	$this->load();
	$this->standalone();
	
    }
   
    private static function f25(&$s) {
	// Mozilla/5.0 iPhone [FBAN/FBIOS;FBDV/iPhone10,5;FBMD/iPhone;FBSN/iOS;FBSV/14.0.1;FBSS/3;FBID/phone;FBLC/en_US;FBOP/5]	
	if (!preg_match('/\[[^\[]*FBSV\/([\d+\.]+)[^\]]*\]/', $s, $m)) return;
	self::sr($m[0], 'v' . $m[1], $s);
	return;
    }
    
    private static function f20(&$s) {
	
	// iPad; CPU OS 13_7 like Mac OS X GSA/128.0.335051718 Mobile/15E148 Safari/604
	
	if (strpos($s, 'iPhone') === false &&
	    strpos($s, 'iPad'  ) === false    ) return;
	$ignore = 233;

	$s = str_replace('iPad; CPU OS ', 'iPad ', $s);	
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
    
        public static function agent20($ain) {
	$a = $ain;
	$a = preg_replace('/ AppleWebKit\/\d+\.\d+ /', ' ', $a);
	$a =  str_replace(' (KHTML, like Gecko) '    , ' ', $a);
	$a =  str_replace(' (compatible; '    , ' ', $a);	
	return $a;
    }
    
    private static function Moz5($ag, $type) {
	static $key = 'Mozilla/5.0';
	static $slk = 11; // assuming Mozilla/5.0
	if ($type === 'is') return substr($ag, 0, $slk);
	if ($type === 'rest') return trim(wsla_agent_p30::sr($key, '', $ag));
	kwas(0, 'bad param - Moz5 anal20');
	
    }
    
    public static function sr($se, $r, &$sub) { $sub = str_replace($se, $r, $sub); return $sub; }
    
    public function getAll() { 
	return ['meta' => $this->meta10, 'user_agents' => $this->biga ];}
    
    public static function aget($p30) { // otherwise conflicts with parent get
	$p30 = self::agent20($p30);
	$p30 = self::Moz5($p30, 'rest');
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
	

	foreach(['Chrome', 'Firefox', 'Safari'] as $v) $p30 = preg_replace('/(' . $v . '\/\d+)[\d\.]*/', '$1', $p30);
	
	if (strpos($p30, 'Android') !== false && strpos($p30, 'Mobile') !== false) self::sr('Mobile', '', $p30);

	self::f20($p30);
	
	self::f27($p30);
	
	self::sr('Version','v', $p30);
	
	$p30 = trim(preg_replace('/\s+/', ' ', $p30));

	
	return $p30;
    }
    
    private static function f27(&$s) {
	// compatible; MSIE 7.0; Win NT 5.1; .NET CLR 1.1.4322 360JK yunjiankong 427698
	self::sr('compatible; ', '', $s);
	if (preg_match('/MSIE [\d\.]\; Win NT \d\.*\d*+/', $s)) {
	    $ignore = 2;
	}
    }
    
    private function sort10($a, $b) {
	return strlen($a) - strlen($b);
    }
    
    private function standalone10() {
	
	static $i = 0;
	
	$a = $this->biga;
	$b = [];
	foreach($a as $r) $b[] = self::aget($r);
	usort($b, [$this, 'sort']);
	if (iscli()) foreach($b as $r) echo($r . "\n");
	
    }
    
    private function sort($a, $b) {
	$t = $a['count'] - $b['count'];
	if ($this->sortDir === 'asc') return $t;
	else return -$t;
    }
    
    private function standalone() { usort($this->biga, [$this, 'sort']);  }
    
    private function alltots() {
	$group =   [	'$group' => [
		'_id' => 'aggdat',
		'count' => ['$sum' => 1],
		'minDate'   => ['$min' => '$ts'],
		'maxDate'   => ['$max' => '$ts'],
		    
		]  ];	
	
	$t = $this->lcoll->aggregate([$group])->toArray();
	$this->meta10 = $t[0];
	return;
    }
    
    private function load() {
	$this->alltots();
	$group =   [	'$group' => [
		'_id' => '$agent',
		'count' => ['$sum' => 1],
		'ts_min' => ['$min' => '$ts'],
		'ts_max' => ['$max' => '$ts'],
		]  ];
	$res = $this->lcoll->aggregate([$group])->toArray();
	$this->biga = $res;
    }
}

if (didCLICallMe(__FILE__)) { new wsla_agent_p30(); }
