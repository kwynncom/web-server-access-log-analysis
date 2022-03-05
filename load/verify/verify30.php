<?php		

// ini_set("memory_limit","4M"); // must be at top of script, I think.

require_once(__DIR__ . '/../config.php');

class wsal_verify_30 extends dao_generic_3 implements wsal_config {
	
	const hashP = __DIR__ . '/../../C/a.out';

	public function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		$this->initdb();
		if (!$worker) {
			$this->getLatest();
		}
		else if (1 || $rn < 3) $this->workit($l, $h, $aa);
	}
	
	private function wdb($lb, $hb, $aa) {
		
		if (amDebugging() && $lb > 0) exit(0); // *****
		
		$qb = ['ftsl1' => $aa[0][0]]; unset($aa);
		
		$i = 0;
		$l = $lb;
		
		do {
			$h = $l + self::chunks;
			if ($h > $hb) $h = $hb;
			
			$q = ['$and' => [['fpp1' => ['$gte' => $l]], ['fpp1' => ['$lte' => $h]], $qb]];
			$c = $this->lcoll->find($q, ['projection' => ['line' => true, '_id' => false]]);

			$s = '';
			foreach($c as $r) $s .= $r['line'];
			fwrite($this->ouh, $s);
			
			$l = $h + 1;
			if ($l > $hb) break;
		} while(++$i < self::nchunks);
	}
	
	private function workit($l, $h, $aa) {

		
		$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
		$io;
		$inpr = proc_open(self::hashP, $pdnonce, $io); unset($pdnonce);
		$this->ouh  = $io[0];
		$inh  = $io[1]; unset($io);
		$this->wdb($l, $h, $aa);
		fclose($this->ouh);
		$xor = fgets($inh);
		fclose($inh); proc_close($inpr);
		file_put_contents($aa[0][1], $xor, FILE_APPEND);
	}
	
	private function initdb() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);		
	}
	
	private function getLatest() {
		$q = [];
		// $q = ['fpp1' => ['$lte' => 10000000]];
		$la = $this->lcoll->findOne($q, ['sort' => ['ftsl1' => -1, 'fpp1' => -1]]);
		
		echo($la['fpp1'] . ' = num chars / last pointer + 1' . "\n");
		$fn = '/tmp/wsal_xor_' . time();
		fork::dofork(true, 0, $la['fpp1'], 'wsal_verify_30', $la['ftsl1'], $fn);
		$this->fxor($fn);
	}
	
	private function fxor($fn) {
		$t = file_get_contents($fn);
		$a = explode("\n", $t);
		$xor = 0;
		foreach($a as $v) {
			if (!is_numeric($v)) continue;
			$n = intval($v);
			$xor ^= $n;
		}
		
		echo("$xor = final XOR\n");
		
	}
	
	public static function shouldSplit(int $l, int $h, int $n) : bool { 
		
		$sz = $h - $l;
		$per = $sz / $n;
		return $per >= self::splitat;
	}
}

if (didCLICallMe(__FILE__)) new wsal_verify_30();

