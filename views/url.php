<?php

require_once('./../load/dao_wsal.php');
require_once('./../bots/bots.php');

class wsal_view extends dao_wsal {
	
	public function __construct() {
		parent::__construct();
		$this->do10();
	}
	
	private function do10() {
		$res = $this->lcoll->find([], ['sort' => ['tsus' => 1, 'n' => 1]]);
		
		$a = [];
		foreach($res as $r) 
			if (!wsal_bots::isBot($r['agent'])) 
				if ($r['httpCode'] < 400) {
					$u = $r['url'];
					if (!isset($a[$u])) $a[$u] = 0;
					$a[$u]++;
				}
		
		asort($a);
		print_r($a);
		return;
	}
	
}

if (didCLICallMe(__FILE__)) {
	new wsal_view();
}
