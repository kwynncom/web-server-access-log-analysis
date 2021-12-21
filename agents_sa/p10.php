<?php

require_once('/opt/kwynn/kwutils.php');

class get_uagents {
	
	const jfp = '/tmp/user_agents_kwc_2021_1_';
	const jfs = '.json';
	// const jfcacheS = -1; // for test
	const jfcacheS = 86400;
	const dbname = 'wsal';
	const quacf = __DIR__ . '/q_uagroup10.js';
	const qmeta = __DIR__ . '/q_meta20.js';
	
	public function __construct() {
		$this->p10();
		$this->p30();
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
		
		self::mongoCLI(self::dbname, self::quacf);
		
		return;
	}
	
	private function p30() {
		$a = self::mongoCLI(self::dbname, self::qmeta);
		return;
	}
	
	public static function processMongoJSON($jin) {
		$res = preg_replace('/NumberLong\("(\d+)"\)/', '$1' , $jin);
		return $res;
	}
	
	public static function mongoCLI($db, $jsp) {
		$cmd = "mongo $db --quiet " .  $jsp;
		echo($cmd);
		$t   = shell_exec($cmd);
//		$l   = strlen($t);
		$a = json_decode(self::processMongoJSON($t), true); kwas(is_array($a), 'mongoCLI did not result in array');
		
		return $a;
		
		
	}
	
}

if (didCLICallMe(__FILE__)) new get_uagents();