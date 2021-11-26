<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao.php');

class bot_cli {
	
	const flin = '/tmp/logs/access.log';
	// const llim = PHP_INT_MAX;
	const llim = 20;
	
	public static function doit() {
		new self();
	}
	
	private function __construct() {
		$this->dao = new dao_wsal();
		$this->do10();
		$this->do20();
		$this->do30();
	}
	
	private function do30() {
		var_dump($this->aga);
	}
	
	private function get() {
		if (!self::llim) return file_get_contents(self::flin);
		$c = 'tail -n ' . self::llim . ' ' . self::flin;
		return shell_exec($c);
		
	}
	
	private function do20() {
		$ra = $this->para;
		// $aaa  = array_column($ra, 'agent');
		foreach($ra as $r) {
			if ($r['httpCode'] >= 400) continue;
			$agr = $r['agent'];
			$ag = $agr;
			// $ag = wsla_agent_p30::aget($agr);
			if (!isset($a[$ag])) $a[$ag] = 0;
			$a[$ag]++;
		}
		
		asort($a);
		$this->aga = $a;
		return;
	}
	
	private function do10() {
		$t = $this->get();
		$md5 = md5($t);
		$l = strlen($t); kwas($l > 100, 'log file too small');
		$ra = explode("\n", trim($t));
		$pa = [];
		$linen = 1;
		foreach($ra as $r) {
			$ta = wsal_parse::parse($r);
			$ta['linen'] = $linen;
			$ta['fmd5'] = $md5;
			$ta['_id'] = $linen . '-' . str_replace(' ', '', $ta['dateHu']);
			$ta['iserr'] = $ta['httpCode'] >= 400 ? true : false;
			
			$pa[] = $ta;
			$linen++;
		} kwas(count($ra) === count($pa), 'unequal log array acount');
		
		$this->dao->putAllLines($pa);
		
		$this->para = $pa;
		return;
		
	}
}

if (didCLICallMe(__FILE__)) bot_cli::doit();