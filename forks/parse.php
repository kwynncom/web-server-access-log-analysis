<?php

class wsal_parse_2022_010 {
	
	const charLimit = 10000; // some jerks will call ~8,000 char lines

	public static function parse($l) {

		$i = 0;
		$ip = $cmd = '';
		do { 
			$c = $l[$i++];
			if ($c === ' ') break;
			else $ip .= $c; 
		} while($i < 50); unset($c);		$i +=  5; 
		
		$hu = substr($l, $i, 26);			$i += 28;
		$msfri = intval(substr($l, $i, 6)); $i +=  8;
		
		while ($i < self::charLimit) { 
			$c = $l[$i++];
			if ($c === '"' && $l[$i - 2] !== '\\') break;
			$cmd .= $c;
		} unset($c);
		
		if ($cmd !== '-') {
			$acmd = explode(' ', $cmd); 
			if (isset($acmd[2])) {
				$verb = $acmd[0];
				$url  = $acmd[1];
				if ($acmd[2] !== 'HTTP/1.1') $unusualHTV = $acmd[2];
			} unset($acmd);
		} $i++;

		$htrc = intval(substr($l, $i, 3)); $i += 4;
		
		if ($l[$i] !== '-') {
			$ss = substr($l, $i, 10);
			preg_match('/^\d+/', $ss , $htrsza);
			$i   += strlen($htrsza[0]);
			$rlen = intval($htrsza[0]); unset($htrsza);
		}  else { $i++; $rlen = 0; } unset($cmd);
		
		preg_match_all('/"([^"]*)/', substr($l, $i), $ms); unset($i);
		$ref     = $ms[1][0];
		$agent   = $ms[1][2]; unset($ms, $ss, $l);

		return get_defined_vars();
	}
}
