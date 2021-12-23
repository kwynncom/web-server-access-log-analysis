<?php

require_once(__DIR__ . '/../load/dao_wsal.php');
require_once(__DIR__ . '/../bots/bots.php');

class wsal_view extends dao_wsal {
	
	const defaultSBack = 110000;
	
	public function __construct() {
		parent::__construct();
		$this->db10();
		$this->do10();
	}
	
	private function db10() {
		$res = $this->lcoll->createIndex(['ts' => 1]);
		return;
	}
	
	private function do10() {
		$res = $this->lcoll->find(['ts' => ['$gt' => time() - self::defaultSBack]], ['sort' => ['tsus' => 1, 'n' => 1]]);
		foreach($res as $a) 
			if (!wsal_bots::isBot($a['agent'])) 
				if ($a['httpCode'] < 400) 
					echo($a['wholeLine'] . "\n");
		return;
	}
	
}

if (didCLICallMe(__FILE__)) {
	new wsal_view();
}
