<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

interface wsal_fork_worker extends fork_worker {
	public static function workit (int $low, int $high, int $workerN, string $fn, string $dbn, string $colnm);	
}


class wsal_worker implements wsal_fork_worker {
		
	const chunks  = 500000;
	const splitat = 100000;
	const nchunks =   1000;

	public static function workit(int $l, int $h, int $rn, $fn, $db, $col) { 
		new self					 ($l,     $h,     $rn, $fn, $db, $col);
	}
	
	public static function shouldSplit(int $l, int $h, int $n) : bool { 
		
		$sz = $h - $l;
		$per = $sz / $n;
		return $per >= self::splitat;
	}
	
	public function __construct($l, $h, $rn, $fnm, $dbn, $cnm) {
		$this->low  = $l;
		$this->high = $h;
		$this->rangen = $rn;
		$this->fnm = $fnm;
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
			$fpb =  ftell($r);
			$rem = $this->high - $fpb;
			if ($rem <= 0) break;
			if ($rem < self::chunks) $tor = $rem;
			else					 $tor = self::chunks;
			$buf  = fread($r, $tor);
			$buf .= $this->chunkpp();
			$this->lineLoop(strtok($buf, "\n"), $fpb);
			
		} while ($chi++ < self::nchunks);
		
		fclose($r);
		
		$tr = $this->iob->ino('done - commit'); // redundant, but getting grand total
		echo("final - worker $this->rangen wrote $tr rows\n");
	}
	
	private function lineLoop($line, $fp0) {
		
		static $lii = 0;
		
		while ($line) {
			++$lii;	
			
			$line .= "\n";
			// echo($line . "\n");
			
			$len = strlen($line);
			$fpp1 = $fp0 + $len;
			$pa = [];
			$pa = wsal_parse_2022_010::parse($line, true);
			$this->put($lii, $line, $fp0, $fpp1, $len, $pa);
			$fp0 += $len;
			$line = strtok("\n");
		}		
						
		$tr = $this->iob->ino('done - commit');
	}
	
	private function put($li, $line, $fp0, $fpp1, $len, $pa) {
		$fts = $this->fts;
		extract($pa); unset($pa);
		$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $li) . '-' . $this->dhu;
		unset($li);
		$this->iob->ino(get_defined_vars());	
	}
} // class

