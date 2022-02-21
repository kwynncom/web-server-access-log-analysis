<?php

require_once('config.php');
require_once('forker.php');

class wsal_verify_20 extends dao_generic_3 implements wsal_config {
	
	const vcoll = 'verify';
	
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(self::vcoll);
		$this->do10();
	}
	
	private function do10() {
		$ts = wsal_load_forks::getL1AndCk(self::lfin);
		$q = "db.getCollection('lines').findOne({'ftsl1' : $ts}, {'sort' : {'fpp1': -1}, 'fpp1' : 1, '_id' : 0})";
		$lptr = dbqcl::q(self::dbname, $q); unset($q);
		$q = "db.getCollection('lines').find({ ftsl1 : $ts, fpp1: { \$lte: $lptr }}).sort({ fpp1 : 1})";
		$q .= '.forEach(function(r) { print(r.line.trim()); })';

//			$s = dbqcl::q($db, $this->lwq, false, false, true, ' | openssl md4 ', true);

		$md4_db = dbqcl::q(self::dbname, $q, false, false, true, ' | openssl md4', true); unset($q);
		
		$ftsl1 = $ts ; unset($ts);
		$fp0 = 0;
		$fpp1 = $lptr; unset($lptr);
		
		$at = time();
		$atr = date('r', $at);
		
		$dat = get_defined_vars();
		
		$this->vcoll->insertOne($dat);
				
		print_r($dat);
		return;
	}
	
}

if (didCLICallMe(__FILE__)) new wsal_verify_20();