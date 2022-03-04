<?php

require_once(__DIR__ . '/../config.php');

class wsal_verify_30 extends dao_generic_3 implements wsal_config {

	public function __construct(bool $worker = false, int $l = -1, int $h = -2, int $rn = -1, ...$aa) {
		if (!$worker) $this->getLatest();
		else if (1 || $rn === 0) $this->workit($l, $h, $aa);
	}
	
	private function workit($l, $h, $aa) {
		$ftsl1 = $aa[0][0];
		$q = "db.getCollection('lines').find({ ftsl1 : $ftsl1, fpp1: { \$gte: $l }, fpp1 : { \$lte : $h  }})";
		$q .= '.forEach(function(r) { print(r.line.trimEnd()); })';
		$res = dbqcl::q(self::dbname, $q, false, false, true, ' | /home/k/sm20/logs/C/a.out');
		echo($res);
	}
	
	private function getLatest() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		$la = $this->lcoll->findOne([], ['sort' => ['ftsl1' => -1, 'fpp1' => -1]]);
		
		fork::dofork(true, 0, $la['fpp1'], 'wsal_verify_30', $la['ftsl1']);

	}
	
	public static function shouldSplit(int $l, int $h, int $n) : bool { 
		
		$sz = $h - $l;
		$per = $sz / $n;
		return $per >= self::splitat;
	}
}

if (didCLICallMe(__FILE__)) new wsal_verify_30();