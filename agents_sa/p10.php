<?php

require_once('/opt/kwynn/kwutils.php');
require_once('bots.php');
require_once('dataConversion.php');

class get_uagents {
	
	const jfp = '/tmp/user_agents_kwc_2021_1_';
	const jfs = '.json';
	// const jfcacheS = -1; // for test
	const jfcacheS = -1;
	const dbname = 'wsal';
	const quacf = __DIR__ . '/q_uagroup10.js';
	const qmeta = __DIR__ . '/q_meta20.js';
	
	private function __construct() {
		$this->p10();
	}
	
	public static function get() {
		$o = new self();
		return $o->getI();
	}
	
	public function getI() { return $this->bigd; }
	
	public static function getjsfp() {
		return self::jfp . get_current_user() . self::jfs;
	}
	
	public static function getJSON($p = false) { 
		if (!$p) $p = self::getjsfp();
		return file_get_contents($p);
		
	}
	
	private function p10() {
		$lo = new sem_lock(__FILE__);
		$lo->lock();
		$p = self::getjsfp();
		$fe = file_exists($p) && (time() - filemtime($p) < self::jfcacheS);
		if ($fe) {
			$this->bigd = json_decode(self::getJSON($p), true);
			return;
		}
		
		$this->p15();
		$this->p20();
		$this->p30();
		$this->p40();
		file_put_contents($p, json_encode($this->bigd));
		$lo->unlock();
	}
	
	private function p15() { dao_wsal_ua_conversion::doit(); }
	
	private function p20() { $this->bigd['agents'] = self::mongoCLI(self::dbname, self::quacf); }
	
	private function p40() {
		$a = $this->bigd['agents'];
		$tot = 0;
		$totpre = $this->bigd['meta']['numLines'];
		$botn = 0;
		foreach($a as $i => $r) {
			$rcnt = $r['count'];
			$tot += $rcnt;
			$isbot = wsal_bots::isBot($r['_id']);
			if ($isbot) $botn += $rcnt;
			$this->bigd['agents'][$i]['isbot'] = $isbot;
		}
		
		kwas($tot === $totpre, 'line count cross check fail wsal lines');
		
		$this->bigd['bot_numLines'] = $botn;
		
		return;
	}
	
	private function p30()		{ 
		$t = self::mongoCLI(self::dbname, self::qmeta); 
		$this->bigd['meta']   = $t[0];
	}
	
	public static function processMongoJSON($jin) { return preg_replace('/NumberLong\("(\d+)"\)/', '$1' , $jin); }
	
	public static function mongoCLI($db, $jsp) {
		$cmd = "mongo $db --quiet " .  $jsp;
		$t   = shell_exec($cmd);
		$a = json_decode(self::processMongoJSON($t), true); kwas(is_array($a), 'mongoCLI did not result in array');
		return $a;
	}
}

if (didCLICallMe(__FILE__)) new get_uagents();