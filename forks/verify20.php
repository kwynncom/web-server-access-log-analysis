<?php

require_once('config.php');
require_once('forker.php');

class wsal_verify_20 extends dao_generic_3 implements wsal_config {
	
	const vcoll = 'verify';
	
	public function __construct() {
		
		parent::__construct(self::dbname);
		$this->creTabs(self::vcoll);
		$vs = $this->init10();
		$this->dbv($vs);
		$this->fv($vs);
	}
	
	private function fv($vsin) {
		extract($vsin); unset($vsin);		
		$c = '';
		$c .= 'goa "';
		$c .= "head -c $fpp1 ";
		$c .= ' /var/log/apache2/access.log ';
		$c .= ' | openssl md4';
		$c .= '"';
		
		$md4_f = self::extractMD(shell_exec($c)); unset($c);
	
		$datd = $dat = get_defined_vars();
		unset($dat['_id']);
		$this->vcoll->upsert(['_id' => $_id], $dat, 1, false);
				
		print_r($datd);
		
		print_r(get_defined_vars());
		
	}
	
	private function dbv($vsin) {
		extract($vsin); unset($vsin);
		$q = "db.getCollection('lines').find({ ftsl1 : $ftsl1, fpp1: { \$lte: $fpp1 }}).sort({ fpp1 : 1})";
		$q .= '.forEach(function(r) { print(r.line.trim()); })';

		$md4_db = self::extractMD(dbqcl::q(self::dbname, $q, false, false, true, ' | openssl md4', true)); unset($q);

		$datd = $dat = get_defined_vars();
		unset($dat['_id']);
		$this->vcoll->upsert(['_id' => $_id], $dat, 1, false);
				
		print_r($datd);
	
	}
	
	private function init10() {
		$ftsl1 = wsal_load_forks::getL1AndCk(self::lfin);
		$q = "db.getCollection('lines').findOne({'ftsl1' : $ftsl1}, {'sort' : {'fpp1': -1}, 'fpp1' : 1, '_id' : 0})";
		$fpp1 = dbqcl::q(self::dbname, $q); 
		unset($q);
		
		$fp0 = 0;
		$_id = date('m-d-H:i:s-Y', $ftsl1) . '_' . $fp0 . '_' . $fpp1;
		
		return get_defined_vars();
	}
	
	public static function extractMD($s) {
		preg_match('/\s[0-9a-f]{32}/', $s, $ms);
		return trim($ms[0]);
	}
	
	public static function shouldSplit (int $low, int $high, int $cpuCount) { return true; }

	
}

if (didCLICallMe(__FILE__)) new wsal_verify_20();