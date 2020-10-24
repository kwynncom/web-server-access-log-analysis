<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_live_load extends dao_generic {
    
    const db = 'wsalogs';
    
    const path     = '/var/log/apache2/';
    const devFile  = 'other_vhosts_access.log';
    const liveFile = 'access.log';
    
    public function __construct() {
	$this->initdb();
	$this->load10();
	$this->p10();
	$this->save10();
    }
    
    public function initdb() {	    
	parent::__construct(self::db);
	$this->lcoll    = $this->client->selectCollection(self::db, 'lines');
	// $this->lcoll->drop();
    }
    
    private function load10() {
	$path = self::path;
	if (isAWS()) $path .= self::liveFile;
	else         $path .= self::devFile ;
	$this->loadDetails20($path);
    }
    
    private function p10() {
	$als  = explode("\n", trim($this->rawt));
	$r = [];
	$i = $this->startn;
	foreach($als as $row) {
	    $t    = wsalParseOneLine($row);
	    $t['n'] = $i++;
	    $r[] = $t;
	}
	$this->a10 = $r;

    }
    
    private function loadDetails20($path) {

	if ($this->lcoll->count() === 0) {
	    $cmd = 'cat';
	    $this->startn = 1;
	} else {
	    $pna = $this->lcoll->findOne([], ['sort' => ['n' => -1], 'projection' => ['_id' => 0, 'n' => 1]]);
	    $pn  = $pna['n'];
	}
	// $this->rawt = shell_exec("$cmd $path");	
    }
    
    private function save10() {
	$this->lcoll->insertMany($this->a10);	
    }
    
}

if (didCLICallMe(__FILE__)) new wsal_live_load();
