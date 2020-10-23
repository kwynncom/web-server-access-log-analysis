<?php

require_once(__DIR__ . '/load/' . 'dao.php');

class wsla_agent_sa30 extends dao_wsal {
    function __construct() {
	parent::__construct();
	$this->load();
	$this->p10();
    }

    private function load() {
	$this->alltots();
	$group =   [
			'$group' => [
			    '_id' => '$agent',
			    'count' => ['$sum' => 1],
			]  
		    ];
	
	$res = $this->lcoll->aggregate([$group])->toArray();
	$this->agagga = $res;
    }   
    
    private function alltots() {
	$group =   [	
			'$group' => [
			    '_id'   => 'grtots',
			    'count' => ['$sum' => 1],
			    'min'   => ['$min' => '$ts'],
			    'max'   => ['$max' => '$ts'],
			]  
		    ];	
	
	$this->allLineTotA = $this->lcoll->aggregate([$group])->toArray();
	return;
    }
    
    private function sort($a, $b) { return $a['count'] - $b['count'];   }
    
    private function p10() {
	usort($this->agagga, [$this, 'sort']);
	print_r($this->agagga);
    }
}

if (didCLICallMe(__FILE__)) { new wsla_agent_sa30(); }