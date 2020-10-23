<?php

require_once('dateFilter.php');
require_once('dao.php');
require_once(__DIR__ . '/../fork/fork.php');
require_once('meta.php');

class wsal_load {
    
    const inpathsfx  = '/tech/logs/current';
    
    const lbpath  = '/tmp/rd/'; // WILL BE DELETED!!!
    const alpath  = self::lbpath . 'all';
    const linesAfter = '2020-10-15';
    
    public function __construct() {
	
	$this->meta = new wsal_meta();
	$this->cpFiles10();
	$this->ilines =  wsalDateFilter::get(self::alpath, self::linesAfter);
	if (1) {
	dao_wsal::clean();
	$this->fork();
	$this->meta->confirm();
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
	foreach($als as $i => $p) {
	    preg_match('/^\S+\s+(.*(\.[^\.]+))$/', $p, $m10);
	    $base = self::lbpath . $i;
	    $to =  $base . $m10[2];
	    $fidq = $this->meta->rdAndCkFile($m10[1], $m10[2], self::linesAfter, $i);
	    
	    if ($fidq === true) continue; 
   
	    kwas(copy($m10[1], $to), 'copy fail cpFiles10()');
	    if ($m10[2] === '.bz2') {
		exec('bzip2 -d ' . $to);
		$this->meta->rdunz($to, $m10[2], $fidq, $i);
	    }

	    $todo++;
	    $cc .= $base . ' ';
	    continue;
	}
	
	if (!$todo) exit(0);
	exec($cc . ' > ' . self::alpath);
	
	return;
	
    }
    
    public function childrenRun($start, $end, $i) {
	file_put_contents('/tmp/ch' . $i, json_encode(get_defined_vars()));
	
	$cmd = 'php ' . __DIR__ . '/' . 'loadWorker.php' . ' '. $this->ilines['tot'] . ' ' . $start . ' ' .  
	$end . ' ' . self::alpath;
	
	exec($cmd);
	
	
    }
    
    private function fork() {
	fork::dofork([$this, 'childrenRun'], $this->ilines['start'], $this->ilines['tot']);	
    }

}

if (didCLICallMe(__FILE__)) new wsal_load();