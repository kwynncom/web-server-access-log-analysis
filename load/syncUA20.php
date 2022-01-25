<?php

require_once(__DIR__ . '/' . 'dao_wsal.php');

class dao_wsal_ua20 extends dao_wsal {
	
	public static function doit() { new self(); }
	
	private function __construct() {
		parent::__construct(self::dbname);
		$this->p10();
	}	
	
	private function p10() {
		$this->creTabs(['a' => 'lines_ua20']);
		$lc = $this->lcoll->count();
		$ac = $this->acoll->count();
		if ($lc === $ac) return;
		$this->p20($ac);
	}
	
	private function p20($maxl) {
				
		$res = $this->lcoll->find(['n' => ['$gt' => $maxl]], ['projection' => ['agent' => 1, 'ts' => 1, 'n' => 1]]);
		$this->acoll->insertMany($res);
		
		// if (0 && time() < strtotime('2021-12-21 21:00')) exit(0);
	}
	
}

