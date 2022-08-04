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
		if (!amDebugging() && 0) $r = fopen('php://stdin', 'r');
		else   $r = popen('tail -n 500 /var/kwynn/mp/m/access.log', 'r');
		$this->ohan = $r;
	}

	private function do10() {
		while ($l = fgets($this->ohan)) {
			$a = wsal_parse_2022_010::parse($l);
			if (wsal_bots::isBot($a['agent'])) continue;
			if ($a['url'] === '/t/9/12/sync/') continue;
			if ($a['htrc']  >= 400) continue;
			if ($a['htrc'] === 302) continue;
			if (preg_match('/\.js$/' , $a['url'])) continue;
			if (preg_match('/\.ico$/', $a['url'])) continue;
			if (!$this->do20($a, $l)) continue;
			
			if (1) $this->do30($a, $l);
			else   $this->out($a, $l);
		}
	} // func
	
	private function do30($a, $l) {
		static $i = 0;
		static $b = [];
		
		$its = strpos($a['url'], 'getTimeSimple.php') !== false;
		$igc = strpos($a['url'], 'getChrony.php'    ) !== false;
		
		if (!($its || $igc)) {
			// $this->out($a, $l);
			return;
		}
		
		$a['l'] = $l;
		$b[$i++ % 3] = $a;
		
		if (count($b) !== 3) /* || (($i++ % 3) !== 2) ) */ return;
		
		if ($its) $itsa = $a;
		
		if ($this->chc2($b)) {
			$this->out($itsa, $itsa['l']);
		} else for ($j=0; $j < 3; $j++) {
			$bi = $i % 3 + $j;
			$this->out($b[$bi], $b[$bi]['l']);
		}
		
		$b = [];
		

	}
	
	private function chc2($b) {
		$m = [];
		$fs = ['agent', 'ip'];
		$s = 0;
		foreach($fs as $f) {
			foreach([-1, 1] as $i) {
				if ($b[1 + $i][$f] === $b[1][$f]) {
					$s++;
					$m[1 + $i] = true;
				}
			}
		}
		
		if ($s === 2)
		foreach([-1, 1] as $i) {
			$k = 1 + $i;
			if (!$m[$k]) {
				$this->out($b[$k], $b[$k]['l']);
				return;
				
			}
		}
		
		return $s === 4;
	}
	
	private function out($a, $l) {
		extract($a);
		echo(date('m/d H:i:s', $ts)); echo(' ');
		printf('%39s', $ip); echo(' ');
		
		printf('%39s', $url); echo(' ');
		

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