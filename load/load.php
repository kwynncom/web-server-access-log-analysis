<?php

require_once('dateFilter.php');
require_once('dao.php');
require_once(__DIR__ . '/../fork/fork.php');
require_once('meta.php');

class wsal_load {
    
    const inpathsfx  = '/tech/logs/current';
    
    public function __construct() {
	$this->meta = new wsal_meta();
	$this->doit();
    }
    
    private function getPaths() {
	$inpath = '/home/' . get_current_user() . self::inpathsfx;
	$c = 'find ' . $inpath . " -type f -name 'acc*' " . ' -printf  "%T+\t%p\n" ' . '  | ' . ' sort ';
	$list = trim(shell_exec($c));
	$als  = explode("\n", $list);
	foreach($als as $timeAndPath) {
	    preg_match('/^\S+\s+(.*(\.[^\.]+))$/', $timeAndPath, $m10);	    
	    kwas(isset($m10[2]), 'match failed m10 cpFiles10 load wsal');
	    $path = $m10[1];
	    $key = '.bz2';
	    if ($m10[2] === $key) {
		shell_exec("bzip2 -d $path");
		$path = preg_replace('/\.bz2$/', '', $path);
	    }
	    
	    $paths[] = $path;
	}
	
	return $paths;
    }
    
    private function doit() {
	foreach(self::getPaths() as $path) {
	    $fia = $this->meta->rdAndCkFile($path);
	    if ($fia === true) continue; 
	    
	    $this->forkDoPath = $path;
	    $this->totLines = $fia['lines'];
	    
	    if (1) {
		
		if ($fia['status'] === 'partial') $startAt = $fia['startAt'];
		else $startAt = 1;
		fork::dofork([$this, 'childrenRun'], $startAt, $this->totLines);
	    	$fia = $this->meta->rdAndCkFile($path);
		kwas($fia === true, 'not loaded status wsal');
	        echo($this->totLines . ' lines loaded from ' . $path . "\n");
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
