<?php

require_once('/opt/kwynn/kwutils.php');

class load20_val extends dao_generic_3 {
	
	const lfin = '/tmp/a6.log';
	
	function __construct() {
		parent::__construct('wsal20');
		$this->creTabs(['l' => 'lines']);
		
		$a = $this->lcoll->find([], ['sort' => ['rn' => 1, 'cn' => 1]]);
		$t = file_get_contents(self::lfin);
		foreach($a as $r) {
			$len = strlen($r['l']);
			try { kwas($r['l'] === substr($t, 0, $len), "mismatch str " . print_r($r, 1));} catch(Exception $ex) {
				throw $ex;
			}
			$t = substr($t, $len);
				
			
		}
		return;
	}
}

if (didCLICallMe(__FILE__)) new load20_val();