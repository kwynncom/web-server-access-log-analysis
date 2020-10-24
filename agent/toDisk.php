<?php

require_once(__DIR__ . '/..' . '/load/' . 'dao.php');
require_once('datToWeb.php');

class wsla_agent_sa30 extends dao_wsal {
    
    function __construct() {
	parent::__construct();
	self::sw();
	$this->aggAgents(); 	self::sw();
	$this->aggAllTots();
	self::sw(); sleep(1); self::sw();
	$this->sortParent();
	$this->save();
    }

    public static function sw() {
	static $t = false;

	$pt = $t;
	$t = self::hrt(1);
	if (!$pt) return;
	$n = self::hrt(1);
	$d = $n - $t;
	$s = number_format($n - $t);
	$t = $n;
    }
    
    public static function hrt() {
	static $f = false;
	if (!$f && function_exists('nanotime')) 
	    $f = 'nanotime';
	else $f = 'hrtime';
	
	return $f(1);
	
    }
    
    private function save() { agent_to_web::save($this->allLineTotA, $this->agagga);    }
    
    private function aggAgents() {

	$group =   [
			'$group' => [
			    '_id' => '$agent',
			    'count' => ['$sum' => 1],
			]  
		    ];
	
	$res = $this->lcoll->aggregate([$group])->toArray();
	$this->agagga = $res;
    }   
    
    private function aggAllTots() {
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