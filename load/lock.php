<?php

require_once('config.php');

class wsal_load_lock extends dao_generic_3 implements wsal_config {
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['pids', self::colla]);
		$this->do10();
	}
	
	private function do10() {
		$uptime = $this->uptime = trim(shell_exec('uptime -s'));
		$this->ftsl1 = $ftsl1 = wsal_getL1AndCk(self::lfin);
		$pid   = getmypid();
		$at    = time();
		$ioa = get_defined_vars(); 
		$this->pcoll->insertOne($ioa); unset($ioa['pid'], $ioa['at']);
		$pidsa = $this->pcoll->find($ioa);
		foreach($pidsa as $pida) {
			$pidd = $pida['pid'];
			if ($pidd === $pid) continue;
			kwas(!posix_getpgid($pidd), "proc $pidd still running\n");
		}
		
		$tires = $this->lcoll->createIndex(['ftsl1' => -1, 'fpp1' => -1]);
		$this->pcoll->deleteMany(['at' => ['$lte' => $at - 300]]);
		
		return;
		
	}
}