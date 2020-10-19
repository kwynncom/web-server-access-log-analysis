<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao.php');

class wsal_load_worker {
    
    const fpre  = '/tmp/rd/kw_wsal_lines_';
    
    public function __construct($ac = 1, $av = []) { 
	$this->p10args($ac, $av);
	$this->load();
	$this->popArr();
	$this->send40();
	// $this->send30();
    }

    private function send40() {
	$dao = new dao_wsal();
	foreach($this->ap10 as $r) $dao->put($r);
    }
    
    private function send30() {
	file_put_contents(self::fpre . $this->av[2], json_encode($this->ap10));
    }

    private function load() {
	$av = $this->av;
	$tl = $av[3]; $this->sa = $sa = $av[4]; $this->ea = $ea = $av[5]; $path = $av[6];
	$bn = $tl - $sa + 1;
	$en = $ea  - $sa + 1;
	$this->lines = trim(shell_exec("tail -n $bn " . $path . " 2> /dev/null | head -n $en "));
	$len = strlen($this->lines);
	return;	
   }
    
   private function popArr() {

	$a = [];
	$i = $this->sa;

	$line = strtok($this->lines, "\n");

	while ($line) {
	    $a[] = wsalParseOneLine($line, 0, $i++);
	    if ($i >= $this->ea) break;
	    $line = strtok("\n");
	}
	$this->ap10 = $a;;
    }
    
    
    private function p10args($ac, $av) {
	switch($ac) {
	    case 1 : self::popTestArgs($av); // $av[1] = 1; $av[3] = 270836; $av[4] = 270827; $av[5] = 270836; $av[6] = '/tmp/access.log'; break;
	    case 7 : break;
	    default: kwas(0, 'bad # params'); break;
	}
	
	$this->av = $av;
    }
    
    private function popTestArgs(&$av) {
	$str = "blah 7250 0 270836 1 270836 /tmp/access.log";
	// $str = "blah 10196 0 270836 270768 270836 /tmp/access.log";
	$av  = explode(" ", $str);
	return;
    }
    
}

if (didCLICallMe(__FILE__)) new wsal_load_worker($argc, $argv);