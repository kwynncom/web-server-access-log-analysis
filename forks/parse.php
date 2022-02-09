<?php

class wsal_parse_in_file {
	
	public function __construct($l) {
		$this->do05($l);
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
		while ($i++ < 1000) { 
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
				if ($i > 1000) return $i;
				if ($htc === '<sc') return 'GET script escape';
				// throw 'unknown line 58';
				if ($cmd === 'dN\x93\xb9\xe6\xbcl\xb6\x92\x84:\xd7\x03\xf1N\xb9\xc5;\x90\xc2\xc6\xba\xe1I-\\') {
					kwynn();
					return 'x93 error';
					}
				kwas(false, 'unknown error line 58');
			}

			$i += strlen($htrsza[0]);
		} else $i++;
		
		$l30 = substr($l, $i);
		
		preg_match_all('/"([^"]+)/', $l30, $ms);
		
		if (!isset($ms[1][2])) {
			kwas(0, 'bad match ref agent');
			kwynn();
		}
		
		
	//	exit(0);
		return $i;

	}
}
