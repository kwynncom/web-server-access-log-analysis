<?php

class wsal_parse_in_file {
	
	const charLimit = 10000; // some jerks will call ~8,000 char lines

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
		
		$srf = false;
		$s = '';
		while ($i++ < self::charLimit) { 
			$c = $l[$i];
			if ($c === '"')
				if ($l[$i - 1] !== '\\') break;
				else $srf = true;
			$s .= $c;
		} unset($c);
		
		if ($srf) {
			$s = str_replace('\"', "'", $s); 
		} unset($srf);

		$cmd = $s; unset($s);
		
		if ($cmd !== '-') {
			$acmd = explode(' ', $cmd); 
			if (isset($acmd[2])) {
				$verb = $acmd[0];
				$url  = $acmd[1];
				if ($acmd[2] !== 'HTTP/1.1') $unusualHTV = $acmd[2];
			} unset($acmd);
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
		
		$ref     = $ms[1][0];
		$agent   = $ms[1][2]; 
		
		unset($ms, $llen);
					
		$ra = get_defined_vars();
		
		return $ra;
	}
}
