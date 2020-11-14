<?php

require_once('dateFilter.php');
require_once('dao.php');
require_once(__DIR__ . '/../fork/fork.php');
require_once('meta.php');
require_once('meta20.php');

class wsal_load {
    
    const inpathsfx  = '/tech/logs/current';
    
    const lbpath  = '/tmp/rd/'; // WILL BE DELETED!!!
    const alpath  = self::lbpath . 'all';
    const linesAfter = '1999-10-15';
    
    public function __construct() {
	
	// *** SEE META NOTE 2020/10/22 END OF DAY **** - in meta file
	// $this->meta = new wsal_meta(); // **** ABOVE ****
	$this->meta = new wsal_meta_20();
	$this->cpFiles10();
	// $this->ilines =  wsalDateFilter::get(self::alpath, self::linesAfter);
	if (0) {
	// dao_wsal::clean();
	// $this->fork();
	// $this->meta->confirm();
	}
    }
    
    private function clearTD() {
	$key = self::lbpath . '*';
	$fs = glob($key);
	if (!$fs) return;
	exec('rm ' . $key);	
    }
    
    private function cpFiles10() {

	$this->clearTD();
	
	
	$inpath = '/home/' . get_current_user() . self::inpathsfx;
	$c = 'find ' . $inpath . " -type f -name 'acc*' " . ' -printf  "%T+\t%p\n" ' . ' | ' . ' sort ';
	$list = trim(shell_exec($c));
	$als  = explode("\n", $list);

	$cc = 'cat '; 
	
	$todo = 0;
	foreach($als as $i => $timeAndPath) {
	    
	    preg_match('/^\S+\s+(.*(\.[^\.]+))$/', $timeAndPath, $m10);
	    kwas(isset($m10[1]), 'match failed m10 cpFiles10 load wsal');
	    
	    $p = $m10[1];
	    
	    if (1) {
		$fia = $this->meta->rdAndCkFile($p);
	    if ($fia === true) continue; 
	    }
   
	    $todo++;
	    // $cc .= $to . ' ';
	    
	    $this->forkDoPath = $p;
	    $this->ilines['tot'] = $fia['lines'];
	    $this->ilines['start'] = 1;
	    $this->fork();
	    
	    $fia = $this->meta->rdAndCkFile($p);
	    kwas($fia === true, 'not loaded status wsal');
	    
	    continue;
	}
	
	if (!$todo) exit(0);
	// exec($cc . ' > ' . self::alpath);
	
	return;
	
    }
    
    public function childrenRun($start, $end, $i) {
	file_put_contents('/tmp/ch' . $i, json_encode(get_defined_vars()));
	
	$cmd = 'php ' . __DIR__ . '/' . 'loadWorker.php' . ' '. $this->ilines['tot'] . ' ' . $start . ' ' .  
	$end . ' ' . $this->forkDoPath;
	
	exec($cmd);
	
	
    }
    
    private function fork() {
	fork::dofork([$this, 'childrenRun'], $this->ilines['start'], $this->ilines['tot']);	
    }

}

if (didCLICallMe(__FILE__)) new wsal_load();
