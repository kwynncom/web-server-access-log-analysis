<?php

require_once(__DIR__ . '/../config.php');

class wsal_verify_30 extends dao_generic_3 implements wsal_config {
	
	const hashP = __DIR__ . '/../../C/a.out';

	public function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		$this->initdb();
		if (!$worker) $this->getLatest();
		else if (1 || $rn < 3) $this->workit($l, $h, $aa);
	}
	
	private function workit($l, $h, $aa) {
		$ftsl1 = $aa[0][0];
		
		$q = ['$and' => [['ftsl1' => $ftsl1], ['fpp1' => ['$gte' => $l]], ['fpp1' => ['$lte' => $h]]]];
		$c = $this->lcoll->findc($q);
		
		$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
		$io;
		$inpr = proc_open(self::hashP, $pdnonce, $io); unset($pdnonce);
		$this->ouh  = $io[0];
		$inh  = $io[1]; unset($io);
		foreach($c as $r) $this->buf($r);
		$this->buf(0, true);
		fclose($this->ouh);
		$xor = fgets($inh);
		fclose($inh); proc_close($inpr);
		file_put_contents($aa[0][1], $xor, FILE_APPEND);
	}
	
	private function buf($r, $flush = false) {
		
		static $i = 0;
		static $s = '';

		if (!$flush) {
			$s .= $r['line'];
			if (++$i < 1000) return;
		}
		
		fwrite($this->ouh, $s);
		$i = 0;
		$s = '';
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