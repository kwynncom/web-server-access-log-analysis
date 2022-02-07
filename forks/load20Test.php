<?php

require_once('/opt/kwynn/kwutils.php');
require_once('worker.php');

class load20_divide extends dao_generic_3 {
	
	const dropUntil = '2022-02-07 08:00';
	
	const lfin = '/tmp/access.log';
	const dbname = 'wsal20';
	const colla   = 'lines';
	
	function __construct() {

		$this->parentLevelDB();
	
		$sz = self::getFSZ(self::lfin);
		
		if (1) fork::dofork(true, 0, $sz - 1, ['log_load_worker', 'doit'], self::lfin, self::dbname, self::colla);
		else {
			$rs = multi_core_ranges::get(0, $sz - 1);
			foreach($rs as $i => $r) $this->doCh20($r['l'], $r['h'], $i);		
		}
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}
	
	private function parentLevelDB() {
		$dd = time() < strtotime(self::dropUntil);
		if (!$dd) return;

		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		$this->lcoll->drop();		
	}
}

new load20_divide();
