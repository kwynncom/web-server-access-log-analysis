<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_parse_in_file {
		
	const charLimit = 10000; // some jerks will call ~8,000 char lines
	const lfin = '/var/kwynn/logs/a14M';
	const chunks = 7 * M_MILLION;
	const maxl = 2000000;

	public function __construct() {
		$this->do10();
		$this->parse();
	}
	
	private function do10() {
		$this->cfp  = 0;
		$this->fsz  = filesize(self::lfin);
		$this->fhan = fopen(self::lfin, 'r');
	}
	
private function parse() {

	// if not 0, then throw away rest of line, as with prev version

	$bp = $i = $this->cfp;

	$l = '';
	
	$ti = 0;
	do {		
		
		if ($ti === 66745) {
			kwynn();
		}
		
		if (!isset($l[$i + self::charLimit])) {
			$l  = substr($l, $i);
			$blen = strlen($l);
			$i  = 0;
			if ($bp + self::chunks < $this->fsz) $tor = self::chunks;
			else								 $tor = $this->fsz - ftell($this->fhan);
			
			if (ftell($this->fhan) >= $this->fsz) return;
			
			if ($tor <= 0) return;
			try {
				if (($ftell = ftell($this->fhan)) !== $bp) {
					kwynn();
				}
				$buf = fread($this->fhan, $tor);
				kwas(isset($buf[$tor - 1]), 'not enough read wsal buf 0359');
			} catch(Exception $ex) {
				$elen = strlen($buf);
				kwynn();
			}
			$l .= $buf;
			$blen = strlen($l);
			
			// if ($tor < self::chunks) exit(0);
			kwynn();
		}
		
		if (!isset($l[$i + 30])) return;
		$ip = '';
		for ($j=0; ($c = $l[$i + $j]) !== false; $j++) {
			if ($c === ' ') break;
			$ip .= $c; 
		}
		
		if ($c === false) {
			kwynn();
		}
		
		unset($c); unset($tor);

		$i += strlen($ip) + 6;

		$hu = substr($l, $i, 26); $i += 26;

		$i += 2;
		$msfri = intval(substr($l, $i, 6)); $i += 6;

		$i += 2;

		$srf = false;
		$s = '';
		for ($j=0; $j < self::charLimit; $j++) { 
			$c = $l[$i + $j];
			if ($c === '"')
				if ($l[$i + $j - 1] !== '\\') break;
				else $srf = true;
			$s .= $c;
		} unset($c);
		
		$i += $j;

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
		} 

		$i += 2;
		$htrc = intval(substr($l, $i, 3)); $i += 4;

		if ($l[$i] !== '-') {
			$ss =  substr($l, $i, 10);
			preg_match('/^\d+/', $ss, $htrsza);
			if (!isset($htrsza[0])) {
				kwynn();
			}
			$i   += strlen($htrsza[0]);
			$rlen = intval($htrsza[0]); unset($htrsza);
		}  else { $i++; $rlen = 0; }
		unset($cmd, $ss);
		
		$i += 2; // first "
		for ($j=0; $l[$i + $j] !== '"'; $j++) ;
		$ref = substr($l, $i, $j);
		
		$i += strlen($ref) + 3;
	
		for ($j=0; $l[$i + $j] !== '"'; $j++)  ;
		$agent = substr($l, $i, $j);
		
		for ($j = 0; $l[$i + $j] !== "\n"; $j++) ;

		$i += $j + 1;
		
		if ($i < $bp) $line = substr($l, 0, $i);
		else		  $line = substr($l, $bp, $i - $bp);
		
		echo($line);
	
		$bp += strlen($line);
	} while(++$ti < self::maxl);
	
	return;
} // func
} // class

if (didCLICallMe(__FILE__)) new wsal_parse_in_file();
