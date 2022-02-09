<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_parse_in_file_20 {
		
	const charLimit = 10000; // some jerks will call ~8,000 char lines
	const lfin = '/var/kwynn/logs/a14M';
	const chunks = 200000;
	const maxl = 1;
	const fileMax = M_BILLION;

	public function __construct() {
		$this->do10();
		$this->do20();
	}
	
	private function do10() {
		$this->cfp  = 0;
		$this->fsz  = filesize(self::lfin);
		$this->fhan = fopen(self::lfin, 'r');
	}

	
	private function do20() {
		$r = $this->fhan;
		$totl = 0;
			
		do { 
			
			$ft = ftell($r);
			if ($ft >= $this->fsz) return;
			$rem = $this->fsz - $ft;
			if (self::chunks < $rem) $tor = self::chunks;
			else					 $tor = $rem;
			
			if ($tor <= 0) return;
			
			$buf  = fread($r, $tor);
			if (ftell($r) >= $this->fsz) return;
			$buf .= fgets($this->fhan);
			$line = strtok($buf, "\n");
			$ti = 0;
			while ($line) {
				$len = strlen($line);
				$totl += $len;
				wsal_parse_in_file::parse($line, ++$ti);
				$line = strtok("\n");
			}
			

		} while ($totl < self::fileMax);
	}
	

} // class

if (didCLICallMe(__FILE__)) new wsal_parse_in_file_20();
