<?php

require_once(__DIR__ . '/..' . '/load/' . 'dao.php');

class wsla_agent_sa30 extends dao_wsal {
    
    const fdir  = '/tmp/';
    const fname = 'kwynn_com_ua_counts.json';
    const path  = self::fdir . self::fname;
    
    function __construct() {
	parent::__construct();
	$this->load();
	$this->p10();
	$this->save();
    }

    private function save() {
	$fa = [];
	$fa['logLineStats'] = $this->allLineTotA;
	$fa['user_agents' ] = $this->agagga;
	$json = json_encode($fa);
	$path = self::path;
	kwas(file_put_contents($path, $json), 'save fail ua counts');
	echo("saved to $path\n");
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
			    '_id'      => 'kwynn.com web server access logs user agent counts',
			    'logLines' => ['$sum' => 1],
			    'minDate'  => ['$min' => '$ts'],
			    'maxDate'  => ['$max' => '$ts'],
			]  
		    ];	
	
	$this->allLineTotA = $this->lcoll->aggregate([$group])->toArray();
	return;
    }
    
    private function sort($a, $b) { return $b['count'] - $a['count'];   }
    
    private function p10() {
	usort($this->agagga, [$this, 'sort']);
	print_r($this->agagga);
    }
}

if (didCLICallMe(__FILE__)) { new wsla_agent_sa30(); }