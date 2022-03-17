<?php

require_once('config.php');
require_once('verify/verify20.php');
require_once('verify/verify30.php');

class wsal_load_forks implements forker, wsal_config {

	function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		if (!$worker) $this->parentConstruct();
		else		 $this->workerConstruct($l, $h, $rn, $aa[0]);
	}
	
	private function parentConstruct() {
		
		$ra20 = wsal_getL1AndCk(self::lfin, true, self::dbname);
		if (!$ra20 && $ra20 !== 0) {
			new wsal_verify_30();
			return;
			
		}
		new wsal_load_lock();
		extract($ra20); unset($ra20); kwas($bpr >= 0, 'this should not fail anymore?  bpr >= 0 wsal');
		$isl = false;
		$epr = $sz - 1;
		$bytes = $epr - $bpr + 1;
		echo('attempting file pointer ' . number_format($bpr) . ' to ' . number_format($epr) . ' / ' . number_format($bytes) . " bytes total\n");
		fork::dofork(true, $bpr, $epr, 'wsal_load_forks', $ftsl1);
		// new wsal_verify_20();
		new wsal_verify_30();
	
	}
	
	public static function shouldSplit(int $l, int $h, int $n) : bool { 
		
		$sz = $h - $l;
		$per = $sz / $n;
		return $per >= self::splitat;
	}
	
	public function workerConstruct($l, $h, $rn, $fts1) {
		$this->low  = $l;
		$this->high = $h;
		$this->rangen = $rn;
		$this->fnm = self::lfin;
		$dbn = self::dbname;
		$cnm = self::colla;
		$this->fts1 = $fts1[0];
		$this->iob = new inonebuf($dbn, $cnm); unset($dbn, $cnm);
		
		$this->do10();
		$this->do20();
	}
	
	private function do10() {
		$fp = $this->cfp  = $this->low;
		$fsz = filesize($this->fnm);
		$this->dhu = date('md-Hi-Y-s', filemtime($this->fnm));
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
			$this->put($lii, $line, $fp0, $fpp1, $len);
			$fp0 += $len;
			$line = strtok("\n");
		}		
						
		$tr = $this->iob->ino('done - commit');
	}
	
	private function put($li, $line, $fp0, $fpp1, $len) {
		
		static $ftsl1 = false;
		
		if ($ftsl1 === false) $ftsl1 = $this->fts1;

		$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $li) . '-' . $this->dhu;	unset($li);

// function      wsal_lineAF(int $ftsl1, int $fp0, int $fpp1, string $line, int $len = 0, string $_id = '') {
		$linea = wsal_lineAF(    $ftsl1,     $fp0,     $fpp1,        $line,     $len,           $_id);
		$this->iob->ino($linea);	
	}
} // class

if (didCLICallMe(__FILE__)) new wsal_load_forks();