<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/../bots/bots.php');
require_once(__DIR__ . '/../utils/parse.php');
class wsal_cli {
	
	public function __construct() {
		$this->setSource();
		$this->do10();
	}
	
	private function setSource() {
		if (!amDebugging() && 0) $r = fopen('php://stdin', 'r');
		else   $r = fopen('php://stdin', 'r');
		$this->ohan = $r;
	}

	private function do10() {
		while ($l = fgets($this->ohan)) {
			$a = wsal_parse::parse($l);
			if (wsal_bots::isBot($a['agent'])) continue;
			if ($a['url'] === '/t/9/12/sync/') continue;
			if ($a['htrc']  >= 400) continue;
			if ($a['htrc'] === 302) continue;
			if (preg_match('/\.js$/' , $a['url'])) continue;
			if (preg_match('/\.ico$/', $a['url'])) continue;
			if (!$this->do20($a, $l)) continue;
			if (strpos($a['url'], 't/21/12/cms/recvJS.php') !== false) continue;
			
			$this->out($a, $l);
		}
	} // func

	
	private function out($a, $l) {
		extract($a);
		echo(date('m/d H:i:s', $ts)); echo(' ');
		printf('%39s', $ip); echo(' ');
		
		printf('%39s', $url); echo(' ');
		

		echo($agent); 
		echo("\n");
	}
	
	private function do20($a) {
		static $b1 = [];
		static $b2 = [];
		static $i = 0;
		if ($b1 === []) $b1[0] = $b1[1] = $b2[0] = $b2[1] = '';
		$s1 = $a['ip'] . $a['url'] . $a['agent'];
		$s2 = $a['ip']             . $a['agent'];
		$i = ($i ^ 1) & 1;
		$b1[$i] = $s1;
		$b2[$i] = $s2;
		
		$k2 = ($i ^ 1) & 1;
		
		if ($b1[$i] === $b1[$k2]) return false;
		if ($b2[$i] === $b2[$k2]) {
			for ($j=0; $j < 2; $j++) {
				$its = strpos($b1[$j], 'getTimeSimple.php') !== false;
				$igc = strpos($b1[$j], 'getChrony.php'    ) !== false;		
				$e = $its || $igc;
				if (!$e) return true;
			}

			return false;
		}
		return true;
	}
	
} // class

new wsal_cli();