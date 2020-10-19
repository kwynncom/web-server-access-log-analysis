<?php

require_once('dateFilter.php');

class wsal_load {
    const alpath  = '/tmp/access.log';
    const linesAfter = '2020-10-15 19:30:00';
    const cpus = 1;
    
    public function __construct() {
	$this->ilines =  wsalDateFilter::get(self::alpath, self::linesAfter);
	$this->ranges = $this->getRanges(self::cpus, $this->ilines['tot'], $this->ilines['start']);
	$this->fork();
    }
    
    private function fork() {
	
	$ppid = getmypid();
	for($i=0; $i < self::cpus; $i++) {
	    $cmd = 'php ' . __DIR__ . '/' . 'loadWorker.php' . " $ppid $i " . ' '. $this->ilines['tot'] . ' ' . $this->ranges[$i]['l'] . ' ' .  
		    $this->ranges[$i]['h'] . ' ' . self::alpath;
	    
	    if (0) {
		$pid = pcntl_fork();
		if ($pid === 0) {

		    // exec("php ");
		    continue;
		}
	    }
	}
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
