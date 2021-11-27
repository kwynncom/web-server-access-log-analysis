<?php


require_once('dao_generic.php');

class agents extends dao_generic_3 {
	
	const dbname = 'wsal';
	
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines', 'm' => 'meta', 'a' => 'agents']);
		if (!isAWS() && 1) $this->acoll->drop();
		$this->acoll->createIndex(['agent' => 1], ['unique' => true]);
		$this->ag10();
		$this->ag20();
	}
	
	private function ag20() {
		$a = $this->aga;
		
		$now = time();
		$dHu = date('r', $now);
		$did = date('Y-m-d-Hi-s');
		
		$oi = 0;
		
		foreach($a as $ag => $n) {
			$d['agent'] = $ag;
			$d['n']     = $n;
			$d['ts_calced'] = $now;
			$d['r_calced' ] = $dHu;
			
			$d['_id'] = 'o-' . ++$oi . '-n-' . $n . '-' . $did;
			
			$this->acoll->insertOne($d);
			
		}
		
	}
	
	private function ag10() {
		
		$noea = $this->lcoll->find(['iserr' => false]);
		
		$this->lnpn = count($noea);
		
		foreach($noea as $r) {
			$agr = $r['agent'];
			$ag = $agr;
			if (!isset($a[$ag])) $a[$ag] = 0;
			$a[$ag]++;
		} unset($r, $ag, $agr);
		
		arsort($a);

		$this->aga = $a;
	}
	
	
}


if (didCLICallMe(__FILE__)) new agents();