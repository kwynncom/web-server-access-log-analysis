<?php

require_once('dao.php');

class dao_wsal_speeders extends dao_wsal {
    
    const linelim = 20000000;
    
    public function __construct() {
	parent::__construct(self::dbName, __FILE__);
	$this->indexes();
	$this->p10();
	$this->p20();
    }
    
    private function p20() {
	$this->res10 = array_reverse($this->res10);
	$i = 0;
	$d = '';
	foreach($this->res10 as $r) {
	    if ($i++ > 0) $d = $r['tsus'] - $p;
	    $p = $r['tsus'];
	    self::outd($d);
	    echo($r['line'] . "\n");
	    continue;
	}
    }
    
    private static function outd($d) {
	$f = '%10';
	if (!is_numeric($d)) {
	    $d  = '-';
	    $f .= 's';
	} else {
	    $d /= 1000;
	    $f .= 'd';
	}
	echo(sprintf($f, $d) . ' ');	
    }
    
    private function indexes() {
	$res = $this->lcoll->createIndex(['ts' => -1, 'tsus' => -1, 'bot' => -1]);
	return;
    }
    
    private function p10() {
	$this->res10 = $this->lcoll->find(['bot' => false, 'err' => false], ['sort' => ['ts' => -1, 'tsus' => -1], 'limit' => self::linelim]);
	$cnt = count($this->res10);
	return;
    }
    
    
}

if (didCLICallMe(__FILE__)) new dao_wsal_speeders();