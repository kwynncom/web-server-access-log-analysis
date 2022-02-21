<?php

require_once('config.php');
require_once('forker.php');

class wsal_verify_20 extends dao_generic_3 implements wsal_config {
	
	const vcoll = 'verify';
	
	public function __construct(bool $isw = false, int $low = -1, int $high = -1, int $workerN = -1) {
		
		parent::__construct(self::dbname);
		$this->creTabs(self::vcoll);
		$vs = $this->init10();
		$this->dbv($vs);
	}
	
	private function dbv($vsin) {
		extract($vsin); unset($vsin);
		$q = "db.getCollection('lines').find({ ftsl1 : $ftsl1, fpp1: { \$lte: $fpp1 }}).sort({ fpp1 : 1})";
		$q .= '.forEach(function(r) { print(r.line.trim()); })';

		$md4_db = self::extractMD(dbqcl::q(self::dbname, $q, false, false, true, ' | openssl md4', true)); unset($q);


		$fp0 = 0;
		
		$at = time();
		$atr = date('r', $at);
		
		$dat = get_defined_vars();
		
		$this->vcoll->insertOne($dat);
				
		print_r($dat);
		return;		
	}
	
	private function init10() {
		$ftsl1 = wsal_load_forks::getL1AndCk(self::lfin);
		$q = "db.getCollection('lines').findOne({'ftsl1' : $ftsl1}, {'sort' : {'fpp1': -1}, 'fpp1' : 1, '_id' : 0})";
	//	$lptr = dbqcl::q(self::dbname, $q); 
		unset($q);
		$fpp1 = 3000;
				
		// dofork(false, 1, 2, 'wsal_verify_20', $ftsl1, $fpp1);
		
		return get_defined_vars();
	}
	
	public static function extractMD($s) {
		preg_match('/\s[0-9a-f]{32}/', $s, $ms);
		return trim($ms[0]);
	}
	
}

if (didCLICallMe(__FILE__)) new wsal_verify_20();