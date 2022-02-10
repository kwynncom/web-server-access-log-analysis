<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_parse_in_file_20 {
		
	const chunks = 500000;
	const fileMax = M_BILLION;

	public static function doit(...$thea) {
		new self($thea);
	}
	
	public function __construct($a5a) {
		$this->low  = $a5a[0];
		$this->high = $a5a[1]; 
		$this->rangen = $a5a[2];
		$this->fnm = $a5a[3][0];
		$dbn = $a5a[3][1];
		$cnm = $a5a[3][2]; unset($a5a);
		$this->iob = new inonebuf($dbn, $cnm); unset($dbn, $cnm);
		
		$this->do10();
		$this->do20();
	}
	
	private function do10() {
		$this->cfp  = $this->low;
		$fsz = filesize($this->fnm);
		$fts = $this->fts = filemtime($this->fnm);
		$this->dhu = date('md-Hi-Y-s', $fts);
		if ($this->high > $fsz) $this->high = $fsz;
		$this->fhan = fopen($this->fnm, 'r');
	}

	
	private function do20() {
		$r = $this->fhan;
		$li = 0;
		$fp = $this->cfp;
				
		fseek($r, $fp);
		
		do { 

			$buf = '';
			
			if ($fp > 0) {
				fseek($r, $fp - 1);
				if (fgetc($r) !== "\n") {
					fgets($r); // throw away

				}
			}
			
			$fpl = $fp = ftell($r);
			
			$rem = $this->high - $fp;
			
			if ($rem <= 0) break;
			
			if ($rem < self::chunks) $tor = $rem;
			else					 $tor = self::chunks;
			
			$buf  = fread($r, $tor);
			$bufsz = strlen($buf);
			$fp += $bufsz;
	
			fseek($r, $fp - 1);
			if (fgetc($r) !== "\n") {
				$rl = fgets($r);
				$fp += strlen($rl);
				$buf .= $rl;
			}
			
			$line = strtok($buf, "\n");

			while ($line) {
				++$li;
				$fp0 = $fpl;
				$llen = strlen($line);
				$fpp1 = $fp0 + $llen;
				
				$pa = wsal_parse_in_file::parse($line, $li, $this->rangen);
				$this->put($li, $line, $fp0, $fpp1, $llen, $pa);
				$line = strtok("\n");
			}

		} while ($fp < self::fileMax);
		
		$this->iob->ino('done - commit');
	}
	
	private function put($li, $line, $fp0, $fpp1, $llen, $pa) {
		
		$rn = $this->rangen;
		extract($pa); unset($pa);
		$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $li) . '-' . $this->dhu;
		$this->iob->ino(get_defined_vars());	
		// exit(0);
		
	}

} // class

if (didCLICallMe(__FILE__)) new wsal_parse_in_file_20();
