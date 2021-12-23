<?php

require_once(__DIR__ . '/../../load/dao_wsal.php');

class dao_wsal_view extends dao_wsal {
	
	const sinceAgoS = 86400 * 30 * 4;
	
	public function __construct() {
		parent::__construct();
		$this->do10();
	}
	
	private function do10() {
		
		$sago = time() - self::sinceAgoS;
		
		$tsq = ' "timed.time" : { "$gte" : ' .  $sago . '}';
		
		$q20 = 'db.getCollection("usage").distinct("ip", { ' . $tsq . '})';
		
		$ar = self::mongoCLI('qemail', $q20, false);
		$a  = array_flip($ar);
		
		$q = <<<'QEUAGG'
db.getCollection('usage').aggregate(
[         { $match : {'email' : {'$ne' : null}, 'timed.time' : { '$gte' :  100   } }}, 
          { $group : { _id : { 'email' : '$email', 'agent' : '$agent', 'ip' : '$ip' } }}   ])
QEUAGG;
		
		$q = str_replace('100', $sago, $q);
		
		$a20 = self::mongoCLI('qemail', $q);
		
		return;
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

if (didCLICallMe(__FILE__)) new dao_wsal_view();

