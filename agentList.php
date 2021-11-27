<?php


require_once('dao_generic.php');

class agents extends dao_generic_3 {
	
	const dbname = 'wsal';
	
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines', 'm' => 'meta']);
		$this->ag10();
	}
	
	private function ag10() {
		
		$totn = $this->lcoll->count();
		$noen = $this->lcoll->find(['iserr' => false]);
		
		foreach($noen as $r) {
			if ($r['iserr']) continue;
			$agr = $r['agent'];
			$ag = $agr;
			if (!isset($a[$ag])) $a[$ag] = 0;
			$a[$ag]++;
		}
		
		asort($a);
		
		var_dump($a);
	}
	
	
}


if (didCLICallMe(__FILE__)) new agents();