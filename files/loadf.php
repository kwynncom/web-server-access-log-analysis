<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/mongodb2.php');

class load_wsal_file extends dao_generic_2 {

    const dbName = 'wsalogs';
    
    public function __construct() {
	parent::__construct(self::dbName, __FILE__);
	$this->creTabs(['l' => 'lines']);
	$thei = $this->lcoll->createIndex(['md5' => 1, 'i' => 1], ['unique' => true]);
	$this->l20();
    }
    
    private function l30($fh) {
	
	$a = [];
	
	while ($l = fgets($fh)) {
	    $a[] = $l;
	    continue;
	} unset($l);
	
	$bigd = [];
	foreach($a as $i => $l) {
	    $l = trim($l);
	    $q['i'] = $i + 1;
	    $q['md5'] = md5($l);
	    $d = $q;
	    $d['line'] = $l;
	    $bigd[] = $d;
	}

	$this->lcoll->insertMany($bigd);
	
    }
    
    private function l20() {
	global $argv;
	global $argc; kwas($argc >= 2, 'not enough args');

	$f = $argv[1]; kwas(file_exists($f), 'file doesn not exist');
	$h = fopen($f, 'r'); kwas($h, 'file open failed');
	$this->l30($h);
	fclose($h);
    }
}

if (didCLICallMe(__FILE__)) new load_wsal_file();
