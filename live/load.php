<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_live_load extends dao_generic {
    
    const db = 'wsalogs';
    
    const path     = '/var/log/apache2/';
    const devFile  = 'other_vhosts_access.log';
    const liveFile = 'access.log';
    
    const lperiter = 100; // lines per iteration
    const maxIter  = 50;  // iterations per process / run - This imposes perhaps too small a limit, but I am trying to avoid any chance of infinite loop
    
    public function __construct() {
	$this->initdb();
	$this->loadInit();
	do {
	    $this->load10();
	    $this->p10();
	    $this->save10();
	} while($this->keepIter());
    }
    
    public function initdb() {	    
	parent::__construct(self::db);
	$this->lcoll    = $this->client->selectCollection(self::db, 'lines');
	$this->lcoll->createIndex(['md5' => 1, 'n' => 1], ['unique' => true]);

    }
    
    private function loadInit() {
	$path = self::path;
	if (isAWS()) $path .= self::liveFile;
	else         $path .= self::devFile ;
	$this->path = $path;

    }
    
    private function p10() {
	
	if (!isset($this->rawt)) die('no logs found - wsal - no rawt');
	
	$als  = explode("\n", trim($this->rawt));
	$r = [];
	
	for ($i = $this->startn - 1; $i < count($als); $i++) {
	    $row = $als[$i];
	    $t    = wsalParseOneLine($row);
	    $t['md5'] = md5($t['line']);
	    $t['n'] = $i + 1;
	    $r[] = $t;
	}

	$this->a10 = $r;

    }
    
    private function checkEndCond($pna) {
	$path = $this->path;
	
	$cmd = "cat -n $path | tail -n 1";
	$t = trim(shell_exec("$cmd"));
	
	preg_match('/^(\d+)\s+(\S.*)/', $t, $m);
	$n = intval($m[1]);
	$l = $m[2];
	
	if (md5($l) === $pna['md5'] && $n === $pna['n']) exit(0);
	return;
    }
    
    private function load10() {
	    $path = $this->path;
	
	if ($this->lcoll->count() === 0) {
	    $cmd = "cat $path";
	    $this->startn = 1;
	} else {
	    $pna = $this->lcoll->findOne([], ['sort' => ['ts' => -1, 'n' => -1], 'projection' => ['_id' => 0, 'n' => 1, 'ts' => 1, 'md5' => 1]]);
	    $pn = $this->startn = $pna['n'];
	    $cmd = "head -n $pn $path | tail -n 1";
	    $t = trim(shell_exec("$cmd"));
	    if (md5($t) !== $pna['md5']) {
		$cmd = "cat $path";
		$this->startn = 1;
	    } else {
		

		$this->checkEndCond($pna);
		
		$hn = $pn + self::lperiter;
		$cmd = "head -n $hn $path";
		$this->startn = $pn + 1;
	    }
	}
	
	$this->rawt = trim(shell_exec("$cmd"));	
	$len = strlen($this->rawt);
	
    }
    
    private function keepIter() {
	if (!isset($this->itern)) $this->itern = 0;
	$this->itern++;
	if ($this->itern > self::maxIter) die('too many iterations');
	return true;
	
	
    }
    
    private function save10() {
	if ($this->a10) $this->lcoll->insertMany($this->a10);	
    }
    
    public static function getFileLines($path) { 
	$t = intval(trim(shell_exec('wc -l < ' . $path))); 
	if (($t < 2)) die('too few or non-numeric  file lines');
    }
}

if (didCLICallMe(__FILE__)) new wsal_live_load();
