<?php

require_once(__DIR__ . '/../load/dao_wsal.php');
require_once(__DIR__ . '/../bots/bots.php');
require_once(__DIR__ . '/myips.php');

class wsal_view extends dao_wsal {
	
	const defaultSBack = 110000;
	
	public function __construct() {
		parent::__construct();
		$this->db10();
		$a20 = $this->do10();
		$this->do20($a20);
	}
	
	private function db10() {
		$res = $this->lcoll->createIndex(['ts' => 1]);
		return;
	}
	
	private function do10() {
		
		$ia = myips::get();
		
		$res = $this->lcoll->find(['ts' => ['$gt' => time() - self::defaultSBack]], ['sort' => ['tsus' => 1, 'n' => 1]]);
		foreach($res as $a) 
			if (!wsal_bots::isBot($a['agent']) && $a['httpCode'] <= 399) {
				$ism = isset($ia[$a['ip']][$a['agent']]);
				if ($ism) continue;// echo('KWU ');
					
				// echo($a['wholeLine'] . "\n");
				$a20[$a['ip']][] = $a;
			}
		return $a20;
	}
	
	private function do20($a) {
		foreach($a as $ip => $ipa) 
			foreach($ipa as $r)
				echo($r['wholeLine'] . "\n");
		
		
	}
	
}

if (didCLICallMe(__FILE__)) {
	new wsal_view();
}
