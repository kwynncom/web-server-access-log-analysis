<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_load_worker {
    public function __construct($ac, $av) { 
	$this->p10args($ac, $av);
	$this->load($av);
	$this->popArr();
	$this->msg();
    }
    
    
    
    private function load($av) {
	$tl = $av[3]; $this->sa = $sa = $av[4]; $this->ea = $ea = $av[5]; $path = $av[6];
	$bn = $tl - $sa + 1;
	$en = $ea  - $sa + 1;
	$this->lines = trim(shell_exec("tail -n $bn " . $path . " 2> /dev/null | head -n $en "));
	return;	
   }
    
   private function popArr() {

	$a = [];
	$i = $this->sa;
	$line = strtok($this->lines, "\n");
	while ($line) {
	    $a[] = wsalParseOneLine($line, 0);
	    if ($i >= $this->ea) break;
	    $line = strtok("\n");
	}
	$this->ap10 = $a;;
    }
    
    
    private function p10args($ac, &$av) {
	switch($ac) {
	    case 1 : $av[3] = 270836; $av[4] = 270827; $av[5] = 270836; $av[6] = '/tmp/access.log'; break;
	    case 7 : break;
	    default: kwas(0, 'bad # params'); break;
	}
    }
    
}

if (didCLICallMe(__FILE__)) new wsal_load_worker($argc, $argv);