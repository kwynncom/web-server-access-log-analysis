<?php

require_once('myips20.php');

class myips  {
	
	// const sinceAgoS = 86400 * 30 * 4;
	const since = '2021-10-01';
	
	private function __construct() {
		$a10 = $this->do10();
		$a20 = $this->do20($a10); unset($a10);
		$a30 = myips_manual_20();
		$this->biga = array_merge($a20, $a30);
	}
	
	public static function get() { 
		$o = new self(); 
		$a = $o->getI(); 
		if (iscli()) var_dump($a);
		return $a;
		
	}
	
	public function getI() { return $this->biga; }
	
	private function do20($ain) {
		$a = [];
		foreach($ain as $rr) {
			$r = $rr['_id'];
			$a[$r['ip']][$r['agent']] = true;
		}
		return $a;
		
	}
	
	private function do10() {
		$a = dbqcl::q('qemail', false, '/var/kwynn/ipq10.js', 'goa');
		return $a;
	}
}

if (didCLICallMe(__FILE__)) myips::get();

