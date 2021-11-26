<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/../doit.php');

class load_wsal_file  {
    
    public function __construct() {
	$this->dao = new dao_wsal();
	$this->l20();
    }
    
    private function l30($fh) {
	
	$a = [];
	
	while ($l = fgets($fh)) {
	    if (!trim($l)) continue;
	    $a[] = $l;
	    continue;
	} unset($l);
	
	$bigd = [];
	foreach($a as $i => $l) {
	    $l = trim($l);
	    if (!$l) continue;
	    $q['i'] = $i + 1;
	    $q['md5'] = md5($l);
	    $d = $q;
	    $d['line'] = $l;
	    
	    if ($this->dao->exists($q)) continue;
	    
	    $d = wsal_21_1::addAnal($d);
	    
	    $bigd[] = $d;
	}

	$this->dao->insertMany($bigd);
	
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
