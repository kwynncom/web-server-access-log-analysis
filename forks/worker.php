<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/' . 'parse.php');
require_once('config_db.php');

class wsal_load_forks implements forker, wsal_db {
		
	const chunks  = 500000;
	const splitat = 100000;
	const nchunks =   1000;

	const lfin = '/var/kwynn/mp/m/access.log';
	// const lfin = '/var/kwynn/logs/a10K';

	function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		if (!$worker) $this->parentConstruct();
		else		 $this->workerConstruct($l, $h, $rn, $aa[0]);
	}
	
	private function parentConstruct() {
		$this->fsz = $sz = self::getFSZ(self::lfin);
		$bpr = $this->ckdb();

		if ($bpr >= 0) {
			$isl = false;
			$epr = $sz - 1;
			$bytes = $epr - $bpr + 1;
			echo('attempting file pointer ' . number_format($bpr) . ' to ' . number_format($epr) . ' / ' . number_format($bytes) . " bytes total\n");
			fork::dofork(false, $bpr, $epr, 'wsal_load_forks', $this->fts1);
		} else {
			$isl = true;
			$bpr = 0;
			$epr = $sz - 1;
		}
		
		// if (time() < strtotime('2022-02-12 21:59'))
		// new wsal_verify(self::dbname, self::colla, self::lfin, $this->fts1, $this->fsz, $bpr, $epr, $isl);
		
		return;		
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}

	private function ckdb() {
		
		$h = $this->fhan = $h = fopen(self::lfin, 'r');
		$l = fgets($h);
		$ts = wsal_parse_2022_010::parse($l, true);
		$this->fts1 = $ts;
		$sz = $this->fsz;

		$q = "db.getCollection('lines').find({'ftsl1' : $this->fts1 }).sort({'fpp1' : -1}).limit(1)";
		$a = dbqcl::q(self::dbname, $q);
		fclose($h);
		
		if (!$a) return 0;
		if ($a['fpp1'] >= $sz) {
			echo("file already loaded\n");
			return -1;
		}
		
		return $a['fpp1'];
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
		$this->fts1 = $fts1;
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