<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao.php');
require_once('load.php');

class wsal_load_worker {
    
    const argsn = 5;
    
    public function __construct($ac = 1, $av = []) { 
	$this->p10args($ac, $av);
	$this->load();
	$this->popArr();
	$this->send40();
    }

    private function send40() {
	$dao = new dao_wsal();
	$dao->putAll($this->ap10);
    }

    private function load() {
	$av = $this->av;
	$tl = intval($av[1]); $this->sa = $sa = intval($av[2]); $this->ea = $ea = intval($av[3]); $path = $av[4];
	$bn = $tl - $sa + 1;
	$en = $ea - $sa + 1;
	$this->lines = shell_exec("tail -n $bn " . $path . " 2> /dev/null | head -n $en ");
	$len = strlen($this->lines);
	return;	
   }
    
   private function popArr() {

	$a = [];
	$i = $this->sa;

	$line = strtok($this->lines, "\n");

	while ($line) {
	    if ($i >= 22568) {
		$ignore = 2;
	    }
	    
	    $a[] = wsalParseOneLine($line, 0, $i);
	    if ($i >= $this->ea) break;
	    $i++;
	    $line = strtok("\n");
	}
	$this->ap10 = $a;;
    }
    
    private function p10args($ac, $av) {
	switch($ac) {
	    case 1 : self::popTestArgs($av); // $av[1] = 1; $av[3] = 270836; $av[4] = 270827; $av[5] = 270836; $av[6] = '/tmp/access.log'; break;
	    case self::argsn : break;
	    default: kwas(0, 'bad # params'); break;
	}
	
	$this->av = $av;
    }
    
    private function popTestArgs(&$av) {
	$str = "blah 281317 1 23443 /tmp/rd/all";
	// $str = "blah 7250 0 270836 112850 135419 /tmp/access.log";

	// $str = "blah 7250 0 270836 248268 270836 /tmp/access.log";
	// $str = "blah 10196 0 270836 270768 270836 /tmp/access.log";
	$av  = explode(" ", $str);
	return;
    }
    
}

if (didCLICallMe(__FILE__)) new wsal_load_worker($argc, $argv);