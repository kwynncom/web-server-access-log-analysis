<?php

require_once('config.php');

class wsal_load_lock extends dao_generic_3 implements wsal_config {
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['pids', self::colla]);
		$this->do10();
	}
	
	private function do10() {
		$ftsl1 = wsal_getL1AndCk(self::lfin);
		$pid   = getmypid();
		$uptime = trim(shell_exec('uptime -s'));
		$this->pcoll->insertOne(get_defined_vars());
		
		
		// only if going to lock
		// $tires = $this->lcoll->createIndex(['ftsl1' => -1, 'fpp1' => -1]);
		
//		$this->pcoll([]);
	}
}