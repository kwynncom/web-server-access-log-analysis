<?php

require_once(__DIR__ . '/../config.php');

class wsal_verify_30 extends dao_generic_3 implements wsal_config {
	
	const hashP = __DIR__ . '/../../C/a.out';

	public function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		if (!$worker) $this->getLatest();
		else if (1 || $rn < 3) $this->workit($l, $h, $aa);
	}
	
	private function workit($l, $h, $aa) {
		$ftsl1 = $aa[0][0];
		$q  = "db.getCollection('lines').find("; 
		$q .= "{ \$and : [ {ftsl1 : $ftsl1}, {fpp1: { \$gte: $l }}, {fpp1 : { \$lte : $h  }}]})";

		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		
		$q = ['$and' => [['ftsl1' => $ftsl1], ['fpp1' => ['$gte' => $l]], ['fpp1' => ['$lte' => $h]]]];
		$c = $this->lcoll->find($q, ['kwnoc' => true]);
		
		$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
		$io;
		$inpr = proc_open(self::hashP, $pdnonce, $io); unset($pdnonce);
		$ouh  = $io[0];
		$inh  = $io[1]; unset($io);
		foreach($c as $row) fwrite($ouh, $row['line']);
		fclose($ouh);
		$xor = fgets($inh);
		fclose($inh); proc_close($inpr);
		file_put_contents($aa[0][1], $xor, FILE_APPEND);
	}
	
	private function workit10($l, $h, $aa) {
		$ftsl1 = $aa[0][0];
		$q  = "db.getCollection('lines').find("; 
		$q .= "{ \$and : [ {ftsl1 : $ftsl1}, {fpp1: { \$gte: $l }}, {fpp1 : { \$lte : $h  }}]})";
		// echo($q . "\n");
		$q .= '.forEach(function(r) { print(r.line.trim()); })';
		$res = dbqcl::q(self::dbname, $q, false, false, true, ' | ' . self::hashP , true);
		// $res = dbqcl::q(self::dbname, $q); 
		echo($res . ' = db res' . "\n");
		file_put_contents($aa[0][1], $res, FILE_APPEND);
	}
	
	private function getLatest() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		$la = $this->lcoll->findOne([], ['sort' => ['ftsl1' => -1, 'fpp1' => -1]]);
		
		echo($la['fpp1'] . ' = num chars / last pointer + 1' . "\n");
		$fn = '/tmp/wsal_xor_' . time();
		fork::dofork(true, 0, $la['fpp1'], 'wsal_verify_30', $la['ftsl1'], $fn);
		$this->fxor($fn);
	}
	
	private function fxor($fn) {
		$t = file_get_contents($fn);
		echo("FILE\n" . $t);
		$a = explode("\n", $t);
		$xor = 0;
		foreach($a as $v) {
			if (!is_numeric($v)) continue;
			$n = intval($v);
			echo("$n = int val\n");
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