<?php

require_once('/opt/kwynn/kwutils.php');
require_once('./../parse.php');

class bot_cli {
	
	const flin = '/tmp/logs/access.log';
	const llim = 1000000000;
	
	public static function doit() {
		new self();
	}
	
	private function __construct() {
		$this->do10();
		$this->do20();
	}
	
	private function get() {
		if (!self::llim) return file_get_contents(self::flin);
		$c = 'tail -n ' . self::llim . ' ' . self::flin;
		return shell_exec($c);
		
	}
	
	private function do20() {
		$ra = $this->para;
		$aaa  = array_column($ra, 'agent');
		foreach($aaa as $ag) {
			if (!isset($a[$ag])) $a[$ag] = 0;
			$a[$ag]++;
		}
		
		arsort($a);
		return;
	}
	
	private function do10() {
		$t = $this->get();
		$l = strlen($t); kwas($l > 100, 'log file too small');
		$ra = explode("\n", trim($t));
		$pa = [];
		foreach($ra as $r) $pa[] = wsal_parse::parse($r); kwas(count($ra) === count($pa), 'unequal log array acount');
		$this->para = $pa;
		return;
		
	}
}

if (didCLICallMe(__FILE__)) bot_cli::doit();