<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_worker {
		
	const chunks  = 500000;
	const nchunks =   1000;

	public static function doit(...$thea) { new self($thea);}
	
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
		$fp = $this->cfp  = $this->low;
		$fsz = filesize($this->fnm);
		$fts = $this->fts = filemtime($this->fnm);
		$this->dhu = date('md-Hi-Y-s', $fts);
		if ($this->high > $fsz) $this->high = $fsz;
		$r = $this->fhan = fopen($this->fnm, 'r');
		fseek($r, $fp);
	}

	private function chunkpp() {
		$r = $this->fhan;
		$p = ftell($r);
		if ($p === 0) return '';
		fseek($r, $p - 1);
		if (fgetc($r) !== "\n") return fgets($r);
		return '';
	}
	
	private function do20() {
		$r = $this->fhan;
		$chi = 0;
				
		do { 
			$buf = '';
			$this->chunkpp();
			$rem = $this->high - ftell($r);
			if ($rem <= 0) break;
			if ($rem < self::chunks) $tor = $rem;
			else					 $tor = self::chunks;
			$buf  = fread($r, $tor);
			$buf .= $this->chunkpp();
			$this->lineLoop(strtok($buf, "\n"));

		} while ($chi++ < self::nchunks);
	}
	
	private function lineLoop($line) {
		
		static $lii = 0;
		$fp0 = ftell($this->fhan);
		
		while ($line) {
			++$lii;	
			echo($line . "\n");
			$llen = strlen($line);
			$fpp1 = $fp0 + $llen;
			$pa = [];
//				$pa = wsal_parse_2022_010::parse($line, $lii, $this->rangen);
			$this->put($lii, $line, $fp0, $fpp1, $llen, $pa);
			$fp0 += $llen;
			$line = strtok("\n");
		}		
						
		$this->iob->ino('done - commit');
	}
	
	private function put($li, $line, $fp0, $fpp1, $llen, $pa) {
		$rn = $this->rangen;
		extract($pa); unset($pa);
		$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $li) . '-' . $this->dhu;
		// $this->iob->ino(get_defined_vars());	
	}
} // class

