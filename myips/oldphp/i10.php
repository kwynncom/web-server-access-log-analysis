<?php

require_once('/opt/kwynn/kwutils.php');
require_once('i20.php');

class myips extends dao_generic_3 {
	
	const dbname = 'myips';
	
	// const sinceAgoS = 86400 * 30 * 4;
	const since = '2021-10-01';
	
	private function do15() {
		return $this->icoll->find();
	}
	
	private function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['i' => 'ips']);
		
		if (iscli()) $a10 = $this->do10();
		else		 $a10 = $this->do15();
		$this->save10($a10);
		$a20 = $this->do20($a10); unset($a10);
		$a30 = myips_manual_20();
		$this->biga = array_merge($a20, $a30);
	}
	
	private function save10($a) {

		foreach($a as $r) {
			$q = $r['_id'];
			$this->icoll->upsert($q, $r);
		}
		
		
		
	}
	
	public static function get() { 
		$o = new self(); 
		$a = $o->getI(); 
		// if (iscli()) var_dump($a);
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
		static $fpre = 'kw_myip_lvd';
		if ((time() < strtotime('2022-01-04 07:00')) && (($j = tuf_get($fpre))) && $j !== 'null') { $a = json_decode($j, true); unset($j); }
		else {
			$a = dbqcl::q('qemail', false, '/var/kwynn/ipq10.js', 'goa');
			tuf_once(json_encode($a), $fpre);
		}
		return $a;
	}
}

if (didCLICallMe(__FILE__)) myips::get();

