<?php

class wsal_parse_in_file {
	
	public function __construct($h) {
		$this->d = new stdClass();
		
		$li = 0;
		do {
			$l = fgets($h); $li++;
			if (!$l) break;
			$this->do05($l);
		}   while($li < 10000);
		
		return;
	}

	function do05($l) {
		$i = 0;
		$ip = '';
		do { 
			$c = $l[$i++];
			if ($c === ' ') break;
			else $ip .= $c; 
		} while($i < 50);
		
		$i += 5;
		
		$hu = substr($l, $i, 26); $i += 26;
		
		$i += 2;
		$msfri = substr($l, $i, 6); $i += 6;
		
		$i += 1;
		
		$s = '';
		while ($i++ < 7000) { 
			$c = $l[$i];
			if ($c === '"') break;
			$s .= $c;
		}
		
		$cmd = $s; unset($s);
		$cmdl = strlen($cmd);
		
		if ($cmd !== '-') $acmd = explode(' ', $cmd);
		
		$i += 2;
		$htc = substr($l, $i, 3); $i += 4;

		if ($l[$i] !== '-') {
			$l20 = substr($l, $i, 10);
			preg_match('/^\d+/', $l20, $htrsza);

			if (!isset($htrsza[0])) {
				kwynn();
			}

			$i += strlen($htrsza[0]);
		} else $i++;
		
		$l30 = substr($l, $i);
		
		preg_match_all('/"([^"]+)/', $l30, $ms);
		
		
	//	exit(0);
		return $i;

	}
}