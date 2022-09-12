<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_parse { // should be a hard link with elsewhere in the path, as of 2022/09/12 03:10
	
	const charLimit = 10000; // some jerks will call ~8,000 char lines
	const mints = 1262954801; // 1262954801 is early in 2010 - older than this incarnation of my site
	const maxThroughUser = 100;

	public static function parse(string $l, bool $tsonly = false, bool $tsasus = false) {

		$i = 0;
		$ip = $cmd = '';
		do { 
			$c = $l[$i++];
			if ($c === ' ') break;
			else if (!$tsonly) $ip .= $c;
		} while($i < 50); unset($c);
		
		while($l[$i++] !== '[' && $i <= self::maxThroughUser) {} // skip over what is usually - - and sometimes is an old-fashioned Apache login
		
		$hu = substr($l, $i, 26);
		
		$ts = strtotime($hu); 
		try { 
			kwas($ts >= self::mints, 'timestamp suspiciously too old - beyond set limit');
		} catch(Exception $ex) {
			kwynn();
			throw $ex;
		}
		if ($tsonly && !$tsasus) return $ts; $i += 28;
		$usfri = intval(substr($l, $i, 6)) ; $i +=  8;
		
		if ($tsonly && $tsasus) return $ts * M_MILLION + $usfri; unset($tsonly);
	
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
