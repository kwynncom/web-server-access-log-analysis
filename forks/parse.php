<?php

class wsal_parse_in_file {
	
	const linelim = 10000;

	public static function parse($l) {

		$llen = strlen($l);

		$i = 0;
		$ip = '';
		do { 
			$c = $l[$i++];
			if ($c === ' ') break;
			else $ip .= $c; 
		} while($i < 50); unset($c);
		
		$i += 5;
		
		$hu = substr($l, $i, 26); $i += 26;
		
		$i += 2;
		$msfri = intval(substr($l, $i, 6)); $i += 6;
		
		$i += 1;
		
		$ci = $i;
		
		$s = '';
		while ($i++ < self::linelim) { 
			$c = $l[$i];
			if ($c === '"' && $l[$i - 1] !== '\\') break;
			$s .= $c;
		} unset($c);
		
		$cmd = $s; unset($s);
		
		if ($cmd !== '-') {
			$acmd = explode(' ', $cmd); 
			if (!isset($acmd[2])) {
				if (substr($cmd, 0, 2) === '\x') $verb = 'hack_escx';
				else kwas(false, 'unaccounted for parse issue wsal 2258');
			} else {
				$verb = $acmd[0];
				$url  = $acmd[1];
				if ($acmd[2] !== 'HTTP/1.1') {
					$unusualHTV = $acmd[2];
				} 
			} unset($acmd);
			
		} else  {
			kwas(false, 'unaccounted for parse issue wsal 2259'); 
		} unset($cmd);

		$i += 2;
		$htrc = intval(substr($l, $i, 3)); $i += 4;

		if ($l[$i] !== '-') {
			preg_match('/^\d+/', substr($l, $i, 10) , $htrsza);
			$i   += strlen($htrsza[0]);
			$rlen = intval($htrsza[0]); unset($htrsza);
		}  else { $i++; $rlen = 0; }
		
		$l30 = substr($l, $i); unset($i, $ci);
		
		preg_match_all('/"([^"]+)/', $l30, $ms); unset($l30);
		
		if (!isset($ms[1][2])) {
			kwas(0, 'bad match ref agent');
			kwynn();
		}
		
		$ref     = $ms[1][0];
		$agent   = $ms[1][2]; unset($ms);
				
		$ra = get_defined_vars();
		
		if (1) return $ra;
		else exit(0);

	}
}
