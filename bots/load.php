<?php

require_once('./../dao_wsal.php');

class dao_agents extends dao_wsal {
	
	public static function get() { 
		$o = new self();
		return $o->getAll();
	}
	
	private function __construct() {
		parent::__construct();
		$this->do10();
	}
	
	private function do10() {
		$a10 = $this->lcoll->aggregate([  [ '$group' => [ '_id'  => '$agent', 'count' => ['$sum' =>  1]] ],
										  [ '$sort'  => ['count' => -1]]		])->toArray();
		
		$this->allaa = $a10; // array_column($a10, '_id');
	}
	
	public function getAll() { return $this->allaa; }
}

if (didCLICallMe(__FILE__)) dao_agents::get();
