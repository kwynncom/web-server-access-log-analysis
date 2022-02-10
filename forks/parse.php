<?php

class wsal_parse_2022_010 {
	
	const charLimit = 10000; // some jerks will call ~8,000 char lines

	public static function parse($l, $pidln, $rn) {

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
		
		if ($hu === "23/Oct/2021:01:38:34 -0400") {
			kwynn();
		}
		
		$i += 2;
		$msfri = intval(substr($l, $i, 6)); $i += 6;
		
		$i += 2;
		
		$srf = false;
		$s = '';
		while ($i < self::charLimit) { 
			if (!isset($l[$i])) {
				
				kwynn();
				return;
			}
			$c = $l[$i++];
			if ($c === '"')
				if ($l[$i - 2] !== '\\') break;
				else $srf = true;
			$s .= $c;
		} unset($c);
		
		if ($srf) {
			// $szb = strlen($l);
			// $s = str_replace('\"', "'", $s); 
		} unset($srf);

		$cmd = $s; unset($s);
		
		if ($cmd !== '-') {
			$acmd = explode(' ', $cmd); 
			if (isset($acmd[2])) {
				$verb = $acmd[0];
				$url  = $acmd[1];
				if ($acmd[2] !== 'HTTP/1.1') $unusualHTV = $acmd[2];
			} unset($acmd);
			
			$i += 1;
		} else {
			kwynn();
			$i += 1;
		}


		$htrc = intval(substr($l, $i, 3)); $i += 4;

		if (!isset($l[$i]))  {
			kwynn();
		}
		
		if ($l[$i] !== '-') {
			$ss = substr($l, $i, 10);
			preg_match('/^\d+/', $ss , $htrsza);
			if (!isset($htrsza[0])) {
				kwynn();
			}
			$i   += strlen($htrsza[0]);
			$rlen = intval($htrsza[0]); unset($htrsza);
		}  else { $i++; $rlen = 0; }
		unset($cmd);
		$l30 = substr($l, $i); unset($i, $ci);
		
		if (!preg_match_all('/"([^"]*)/', $l30, $ms)) {
			kwynn();
		}
		unset($l30);
		
		if (!isset($ms[1][2])) {
			kwynn();
		}
		$ref     = $ms[1][0];
		$agent   = $ms[1][2]; 

		if (0 && $rn === 11 && ($pidln % 1000 === 0)) {
			kwynn();
		}
		
		unset($ms, $llen);
		unset($ss, $l, $rn, $pidln);
					
		$ra = get_defined_vars();

		return $ra;
	}
}
