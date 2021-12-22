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
		if ($this->lcoll->count() === $this->acoll->count()) return;
		$this->p20();
	}
	
	private function p20() {
		
		$this->lcoll->find();
	}
	
}

