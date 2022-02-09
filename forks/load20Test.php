<?php

require_once('/opt/kwynn/kwutils.php');
// require_once('worker.php');
require_once('parse20.php');

class load20_divide extends dao_generic_3 {
	
	const dropUntil = '2022-02-10 08:00';
	
	const lfin = '/var/kwynn/logs/a400M';
	const dbname = 'wsal20';
	const colla   = 'lines';
	
	function __construct() {
		$this->parentLevelDB();
		$sz = self::getFSZ(self::lfin);
		fork::dofork(true, 0, $sz - 1, ['wsal_parse_in_file_20', 'doit'], self::lfin, self::dbname, self::colla);
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
