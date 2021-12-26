<?php

require_once('myips20.php');

class myips  {
	
	// const sinceAgoS = 86400 * 30 * 4;
	const since = '2021-10-01';
	
	private function __construct() {
		$a10 = $this->do10();
		$a20 = $this->do20($a10); unset($a10);
		$a30 = myips_manual_20();
		$this->biga = array_merge($a20, $a30);
	}
	
	public static function get() { 
		$o = new self(); 
		$a = $o->getI(); 
		if (iscli()) var_dump($a);
		return $a;
		
	}
	
	public function getI() { return $this->biga; }
	
	private function do20($ain) {
		$a = [];
		foreach($ain as $rr) {
			$r = $rr['_id'];
			$a[$r['ip']][$r['agent']] = true;
		}
		return $a;
		
	}
	
	private function do10() {
		
		// $sago = time() - self::sinceAgoS;
		$sago = strtotime(self::since);
		
		$q = <<<'QEUAGG'
db.getCollection('usage').aggregate(
[         { $match : {'email' : 'bob@example.com', 'timed.time' : { '$gte' :  100   } }}, 
          { $group : { _id : {  'agent' : '$agent', 'ip' : '$ip' } }}   ])
QEUAGG;
		
		$em = trim(file_get_contents('/var/kwynn/myemail.txt'));
		
		$q = str_replace('bob@example.com', $em, $q);
		$q = str_replace('100', $sago, $q);
		
		$a = self::mongoCLI('qemail', $q);
		
		return $a;
	}
	
	public static function mongoCLI($db, $q, $toArray = true) {
		
		$p = '/tmp/qeq10_2021_' . md5($q) . '_' . get_current_user() . '.js';
		
		if (!file_exists($p)) {
			file_put_contents($p, '');
			chmod($p, 0600);
			$q  = 'printjson(' . $q;
			if ($toArray)
				$q .=  '.toArray()';
			$q .= ')';
			file_put_contents($p, $q);
		}
		
		$cmd = "mongo $db --quiet $p";
		$t   = shell_exec($cmd);
		$a = json_decode($t, true); kwas(is_array($a), 'mongoCLI did not result in array');
		return $a;
		
	}
}

if (didCLICallMe(__FILE__)) myips::get();

