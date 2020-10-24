<?php

require_once(__DIR__ . '/..' . '/load/' . 'dao.php');
require_once('datToWeb.php');

class wsla_agent_sa30 extends dao_wsal {
    
    function __construct() {
	parent::__construct();
	$this->aggAgents();
	$this->aggAllTots();	// self::sw('after agg tots'); 
	$this->sortParent();
	$this->save();
    }

    public static function sw($m = '') {
	static $t = false;

	if (!$t) { $t = hrtime(1); return; }
	$n = hrtime(1);
	$d = $n - $t;
	$f = number_format($n - $t);
	$debugHere = true;
	$t = hrtime(1);
    }
    
    private function save() { agent_to_web::save($this->allLineTotA, $this->agagga);    }
    
    private function aggAgents() {

	// $i1  = $this->lcoll->createIndex(['md5ag' =>   1]);	
	// $in1 = $this->lcoll->createIndex(['md5ag' =>  -1]);
	
	$group =   [
			'$group' => [
			    '_id' => '$md5ag',
			    // 'count' => ['$sum' => 1],
			]  
		    ];
	
	// self::sw('before ua agg');
	$res = $this->lcoll->aggregate([$group], /* ['hint' => $i1]*/)->toArray();
	// self::sw('after ua agg');
	// $this->agagga = $res;
    }   
    
    private function aggAllTots() {
	
	$in1 = $this->lcoll->createIndex(['ts' =>  1]);
	// $in2 = $this->lcoll->dropIndex($in1);	
	$in2 = $this->lcoll->createIndex(['ts' => -1]);
	// $in2 = $this->lcoll->dropIndex($in2);	
	// self::sw('after ci');
	
	$group =   [	
			'$group' => [
			    '_id'      => agent_to_web::aggLabel,
			    // 'lines'    => ['$sum' => 1    ],
			    'minDate'  => ['$min' => '$ts'],
			    'maxDate'  => ['$max' => '$ts'],
			]  
		    ];	
	
	$cnt = $this->lcoll->count();
	self::sw('before date sort');
	$max = $this->lcoll->findOne([], ['sort' => ['ts' => 1]]);
	self::sw('after date sort');
	$ta = $this->lcoll->aggregate([$group])->toArray();
	$this->allLineTotA = $ta[0];
	return;
    }
    
    private function sort($a, $b) { return $b['count'] - $a['count'];   }
    
    private function sortParent() { usort($this->agagga, [$this, 'sort']); }
}

if (didCLICallMe(__FILE__)) { new wsla_agent_sa30(); }