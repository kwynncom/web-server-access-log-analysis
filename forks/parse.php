<?php

class wsal_parse_in_file {
	
	public function __construct($h) {
		$this->d = new stdClass();
		
		$li = 0;
		do {
			
			$this->ipf($h);
			$this->uf($h);
			$this->t10($h);
			$this->do20($h);
			$l = fgets($h); $li++;
			if (!$l) {
				exit(0);
			}
		} while($li < 100000);
		
		return;
	}
	
	function do20($h) {
		fgetc($h);
		$s = '';

		for ($i=0; $i < 200; $i++) { 
			$c = fgetc($h);
			if ($c === '"') break;
			$s .= $c;
		}
		
		if ($s === '-') return; // 408
		
		$a = explode(' ', $s);
		try {
			kwas(count($a) === 3, 'bad count wsal cmd 2313');
		} catch (Exception $ex) {
			kwynn();
		}
		
		return;
	}
	
	function t10($h) {
		// 12345678901234567890123456
		// 23/Oct/2021:01:38:34 -0400
		$hu = fread($h, 26);
		fread($h, 2);
		$usfri = fread($h, 6);
		fgetc($h);
		return;
		
	}
	
	function uf($h) { fread($h, 5); }
	

	function ipf($h) {
		$i = 0;
		$ip = '';
		do { 
			$c = fgetc($h);
			if ($c === ' ') break;
			else $ip .= $c; 
		} while($i++ < 50);
		
		// $this->d->ip = $ip;
	}
}