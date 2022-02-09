<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_parse_in_file_20 {
		
	const lfin = '/var/kwynn/logs/a14M';
	const chunks = 500000;
	const fileMax = M_BILLION;

	public function __construct($low = 0, $high = self::fileMax) {
		$this->low = $low;
		$this->high = $high;
		$this->do10();
		$this->do20();
	}
	
	private function do10() {
		$this->cfp  = $this->low;
		$fsz = filesize(self::lfin);
		if ($this->high > $fsz) $this->high = $fsz;
		$this->fhan = fopen(self::lfin, 'r');
	}

	
	private function do20() {
		$r = $this->fhan;
		$li = 0;
		$fp = $this->cfp;
		
		fseek($r, $fp);
		
		do { 
			if ($fp >= $this->high) return;
			
			$buf  = fread($r, self::chunks);
			$bufsz = strlen($buf);
			$fp += $bufsz;

			$i = 0;
			
			fseek($r, $fp - 1);
			if (fgetc($r) !== "\n") {
				$rl = fgets($r);
				$fp += strlen($rl);
				$buf .= $rl;
			}
			
			$line = strtok($buf, "\n");

			while ($line) {
				++$li;
				wsal_parse_in_file::parse($line, $li);
				$line = strtok("\n");
			}
			

		} while ($fp < self::fileMax);
	}
	

} // class

if (didCLICallMe(__FILE__)) new wsal_parse_in_file_20();
