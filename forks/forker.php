<?php

require_once('config.php');

class wsal_load_forks implements forker, wsal_config {
		
	const chunks  = 500000;
	const splitat = 100000;
	const nchunks =   1000;

	function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		if (!$worker) $this->parentConstruct();
		else		 $this->workerConstruct($l, $h, $rn, $aa[0]);
	}
	
	private function parentConstruct() {
		 $sz = self::getFSZ(self::lfin);
		
		$ra20 = self::getL1AndCk(self::lfin, $sz, self::dbname);
		if (!$ra20) return;
		extract($ra20); unset($ra20);

		if ($bpr >= 0) {
			$isl = false;
			$epr = $sz - 1;
			$bytes = $epr - $bpr + 1;
			echo('attempting file pointer ' . number_format($bpr) . ' to ' . number_format($epr) . ' / ' . number_format($bytes) . " bytes total\n");
			fork::dofork(false, $bpr, $epr, 'wsal_load_forks', $fts1);
		} else {
			$isl = true;
			$bpr = 0;
			$epr = $sz - 1;
		}
		
		return;		
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}

	public static function getL1AndCk($fname, $sz = false, $dbname = false) {
		
		$h = fopen($fname, 'r');
		$l = fgets($h);
		fclose($h);
		$ts = wsal_parse_2022_010::parse($l, true);
		
		if ($sz === false) return $ts;

		$q = "db.getCollection('lines').find({'ftsl1' : $ts }).sort({'fpp1' : -1}).limit(1)";
		$a = dbqcl::q($dbname, $q);
		
		if (!$a) return 0;
		if ($a['fpp1'] >= $sz) {
			echo("file already loaded\n");
			return false;
		}
		
		return ['bpr' => $a['fpp1'], 'fts1' => $ts];
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
			$pa = [];
			$this->put($lii, $line, $fp0, $fpp1, $len, $pa);
			$fp0 += $len;
			$line = strtok("\n");
		}		
						
		$tr = $this->iob->ino('done - commit');
	}
	
	private function put($li, $line, $fp0, $fpp1, $len, $pa) {
		
		static $ftsl1 = false;
		
		if ($ftsl1 === false) $ftsl1 = $this->fts1;
		
		extract($pa); unset($pa);
		$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $li) . '-' . $this->dhu;
		unset($li);
		$this->iob->ino(get_defined_vars());	
	}
} // class

if (didCLICallMe(__FILE__)) new wsal_load_forks();