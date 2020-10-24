<?php

require_once(__DIR__ . '/..' . '/load/' . 'dao.php');
require_once('datToWeb.php');

class wsla_agent_sa30 extends dao_wsal {
    
    function __construct() {
	parent::__construct();
	$this->load();
	$this->sortParent();
	$this->save();
    }

    private function save() { agent_to_web::save($this->allLineTotA, $this->agagga);    }
    
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
			    '_id'      => agent_to_web::aggLabel,
			    'lines'    => ['$sum' => 1    ],
			    'minDate'  => ['$min' => '$ts'],
			    'maxDate'  => ['$max' => '$ts'],
			]  
		    ];	
	
	$ta = $this->lcoll->aggregate([$group])->toArray();
	$this->allLineTotA = $ta[0];
	return;
    }
    
    private function sort($a, $b) { return $b['count'] - $a['count'];   }
    
    private function sortParent() { usort($this->agagga, [$this, 'sort']); }
}

if (didCLICallMe(__FILE__)) { new wsla_agent_sa30(); }