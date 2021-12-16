<?php

require_once(__DIR__ . '/../load/dao_wsal.php');
require_once(__DIR__ . '/../bots/bots.php');

class wsal_view extends dao_wsal {
	
	public function __construct() {
		parent::__construct();
		$this->do10();
	}
	
	private function do10() {
		$res = $this->lcoll->find([], ['sort' => ['tsus' => 1, 'n' => 1]]);
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
