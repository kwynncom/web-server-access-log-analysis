<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_live_load extends dao_generic { // **** WILL NOT YET WORK IF > 100 LINES ADDED
    
    const db = 'wsalogs';
    
    const path     = '/var/log/apache2/';
    const devFile  = 'other_vhosts_access.log';
    const liveFile = 'access.log';
    
    public function __construct() {
	$this->initdb();
	$this->loadInit();
	$this->load10();
	$this->p10();
	$this->save10();
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
    
    private function load10() {
	    $path = $this->path;
	
	if ($this->lcoll->count() === 0) {
	    $cmd = "cat $path";
	    $this->startn = 1;
	} else {
	    $pna = $this->lcoll->findOne([], ['sort' => ['n' => -1], 'projection' => ['_id' => 0, 'n' => 1, 'ts' => 1, 'md5' => 1]]);
	    $pn = $this->startn = $pna['n'];
	    $cmd = "head -n $pn $path | tail  -n 1";
	    $t = trim(shell_exec("$cmd"));
	    if (md5($t) !== $pna['md5']) {
		$cmd = "cat $path";
		$this->startn = 1;
	    } else {
		$hn = $pn + 100;
		$cmd = "head -n $hn $path";
		$this->startn = $pn + 1;
	    }
	}
	
	$this->rawt = trim(shell_exec("$cmd"));	
	$len = strlen($this->rawt);
	
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
