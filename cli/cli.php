<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/../bots/bots.php');
require_once(__DIR__ . '/../load/utils/parse.php');
class wsal_cli {
	
	public function __construct() {
		$this->setSource();
		$this->do10();
	}
	
	private function setSource() {
		if (!amDebugging()) $r = fopen('php://stdin', 'r');
		else   $r = popen('tail -n 100 /var/kwynn/mp/m/access.log', 'r');
		$this->ohan = $r;
	}

	private function do10() {
		while ($l = fgets($this->ohan)) {
			$a = wsal_parse_2022_010::parse($l);
			if (wsal_bots::isBot($a['agent'])) continue;
			if ($a['url'] === '/t/9/12/sync/') continue;
			if (preg_match('/\.js$/' , $a['url'])) continue;
			if (preg_match('/\.ico$/', $a['url'])) continue;
			if (!$this->do20($a, $l)) continue;
			$this->out($a, $l);

		}
	} // func
	
	private function out($a, $l) {
		extract($a);
		echo(date('m/d H:i:s', $ts)); echo(' ');
		printf('%39s', $ip); echo(' ');
		echo($url); echo(' ');
		echo($agent); 
		echo("\n");
	}
	
	private function do20($a) {
		static $b = [];
		static $i = 0;
		if ($b === []) $b[0] = $b[1] = '';
		$s = $a['ip'] . $a['url'] . $a['agent'];
		$i = ($i ^ 1) & 1;
		$b[$i] = $s;
		if ($b[$i] === $b[($i ^ 1) & 1]) return false;
		return true;
		
		
	}
	
} // class

new wsal_cli();