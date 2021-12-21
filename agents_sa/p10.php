<?php

require_once('/opt/kwynn/kwutils.php');

class get_uagents {
	
	const jfp = '/tmp/user_agents_kwc_2021_1_';
	const jfs = '.json';
	const jfcacheS = -1; // for test
	// const jfcacheS = 86400;
	const dbname = 'wsal';
	const quacf = __DIR__ . '/q_uagroup10.txt';
	
	public function __construct() {
		$this->p10();
	}
	
	private static function getjsfp() {
		return self::jfp . get_current_user() . self::jfs;
	}
	
	private function p10() {
		$lo = new sem_lock(__FILE__);
		$lo->lock();
		$p = self::getjsfp();
		$rc = file_exists($p) && (time() - filemtime($p) < self::jfcacheS);
		if (!$rc) file_put_contents($p, '');
		$lo->unlock();
		if ($rc) return;
		
		$this->p20($p);
	}
	
	private function p20($path) {
		
		$q = file_get_contents(self::quacf);
		self::mongoCLI(self::dbname, $q);
		
		return;
	}
	
	public static function mongoCLI($db, $q) {
		
	}
	
}

if (didCLICallMe(__FILE__)) new get_uagents();