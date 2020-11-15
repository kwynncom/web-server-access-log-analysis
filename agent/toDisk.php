<?php

require_once(__DIR__ . '/..' . '/load/' . 'dao.php');
require_once('datToWeb.php');
require_once(__DIR__ . '/../fullStack1/bots.php');

class wsla_agent_sa30 extends dao_wsal {
    
    function __construct() {
	parent::__construct();
	$this->aggAgents();
	$this->aggAllTots();
	$this->sortParent();
	$this->p10();
	$this->save();
    }

    private function save() { agent_to_web::save($this->allLineTotA, $this->agagga);    }
    
    private function p10() {
	foreach($this->agagga as $i => $r) {
	    $this->agagga[$i]['bot'] = isBot30($r['_id']);
	    continue;
	}
	return;
    }
    
    private function aggAgents() {
	
	$this->matcha['$match'] = ['httpcode' => ['$gte' => 200, '$lte' => 399], 'ext' => ['$nin' => ['ico', 'jpg', 'gif', 'png', 'css', 'js']]];
	
	$f[] = $this->matcha;
	
	$f[] = ['$group' => ['_id' => '$agent', 'count' => ['$sum' => 1]]];
	$res = $this->lcoll->aggregate($f)->toArray();
	$this->agagga = $res;
	return;
    }   
    
    private function aggAllTots() {
	
	$in1 = $this->lcoll->createIndex(['ts' =>  1]);
	$in2 = $this->lcoll->createIndex(['ts' => -1]);
	
	$group =   [	    '$group' => [
			    '_id'      => agent_to_web::aggLabel,
			    'lines'    => ['$sum' => 1    ],
			    'minDate'  => ['$min' => '$ts'],
			    'maxDate'  => ['$max' => '$ts'],
			]  	    ];	
	
	$cnt = $this->lcoll->count();
	$max = $this->lcoll->findOne([], ['sort' => ['ts' => 1]]);
	$f[] = $this->matcha;
	$f[] = $group;
	$ta = $this->lcoll->aggregate($f)->toArray();
	$this->allLineTotA = $ta[0];
	return;
    }
    
    private function sort($a, $b) { return $b['count'] - $a['count'];   }
    
    private function sortParent() { usort($this->agagga, [$this, 'sort']); }
}

if (didCLICallMe(__FILE__)) { new wsla_agent_sa30(); }