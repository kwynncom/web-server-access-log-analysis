<?php

require_once('dateFilter.php');
require_once('dao.php');

class wsal_load {
    const alpath  = '/tmp/rd/access.log';
    const linesAfter = '2019-10-13 19:30:00';
    const cpus = 12;
    
    public function __construct() {
	$this->ilines =  wsalDateFilter::get(self::alpath, self::linesAfter);
	$this->ranges = $this->getRanges(self::cpus, $this->ilines['tot'], $this->ilines['start']);
	dao_wsal::clean();
	$this->fork();
    }
    
    private function fork() {
	
	$cpids = [];
	$ppid = getmypid();
	for($i=0; $i < self::cpus; $i++) {
	    $cmd = 'php ' . __DIR__ . '/' . 'loadWorker.php' . " $ppid $i " . ' '. $this->ilines['tot'] . ' ' . $this->ranges[$i]['l'] . ' ' .  
		    $this->ranges[$i]['h'] . ' ' . self::alpath;
	    
	    $pid = pcntl_fork();
	    if ($pid === 0) {
		exec($cmd);
		exit(0);
	    } else {
		$cpids[] = $pid;
		continue;
	    }
	}
	
	for($i=0; $i < self::cpus; $i++) pcntl_waitpid($cpids[$i], $status);
	    
	return;
    }
    
    private function getRanges($cpus, $totlines, $startAt) { 

	$ranges = [];
	
	$tdl = $totlines - $startAt + 1;

	for ($i=0; $i < $cpus; $i++) {

	    if ($i === 0) $ranges[$i]['l'] = $i + $startAt;
	    if ($i < ($cpus - 1)) {
		$ranges[$i]['h'] = intval(round(($tdl / $cpus) * ($i + 1))) + $startAt;   
		$ranges[$i + 1]['l'] = $ranges[$i]['h'] + 1;    
	    } else $ranges[$i]['h'] = $tdl + $startAt - 1;

	    $l = $ranges[$i]['l'];
	    $h = $ranges[$i]['h'];
	}

	return $ranges;
    }
}

if (didCLICallMe(__FILE__)) new wsal_load();
