<?php

require_once('dateFilter.php');
require_once('dao.php');
require_once(__DIR__ . '/../fork/fork.php');
require_once('meta.php');

/******** 2020/11/13 11:53pm - next step is to figure out where to start as incremental, partially redundant files are introduced  *****/

class wsal_load {
    
    const inpathsfx  = '/tech/logs/current';
    
    public function __construct() {
	$this->meta = new wsal_meta();
	$this->doit();
    }
    
    private function doit() {
	$inpath = '/home/' . get_current_user() . self::inpathsfx;
	$c = 'find ' . $inpath . " -type f -name 'acc*' " . ' -printf  "%T+\t%p\n" ' . ' | ' . ' sort ';
	$list = trim(shell_exec($c));
	$als  = explode("\n", $list);
	
	foreach($als as $i => $timeAndPath) {
	    
	    preg_match('/^\S+\s+(.*(\.[^\.]+))$/', $timeAndPath, $m10);
	    kwas(isset($m10[1]), 'match failed m10 cpFiles10 load wsal');
	    
	    $p = $m10[1];
	    $fia = $this->meta->rdAndCkFile($p);
	    if ($fia === true) continue; 
	    
	    $this->forkDoPath = $p;
	    $this->totLines = $fia['lines'];
	    if (1) {
		fork::dofork([$this, 'childrenRun'], 1, $this->totLines);
	    	$fia = $this->meta->rdAndCkFile($p);
		kwas($fia === true, 'not loaded status wsal');
	        echo($this->totLines . ' lines loaded from ' . $p . "\n");
	    }
	    
	    continue;
	}
    }

    public function childrenRun($start, $end, $i) {
	$cmd = 'php ' . __DIR__ . '/' . 'loadWorker.php' . ' '. $this->totLines . ' ' . $start . ' ' .  $end . ' ' . $this->forkDoPath;
	exec($cmd);
    }
}

if (didCLICallMe(__FILE__)) new wsal_load();
