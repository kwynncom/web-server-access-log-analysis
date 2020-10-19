<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_load_worker {
    
    const mtid = 1;
    const doSerial = false;
    
    public function __construct($ac, $av) { 
	$this->p10args($ac, $av);
	$this->load();
	$this->popArr();
	$this->msg();
    }

    private function msg() {
	$av = $this->av;
	$ppid = intval($av[1]); kwas($ppid > 0, 'bad parent id');
	$msgh = msg_get_queue($ppid, 0600);
        $str = json_encode($this->ap10);
        // file_put_contents('/tmp/child', $str);
	$res = msg_send($msgh, self::mtid, "hi", 1);
	return;
    }
    
    private function load() {
	$av = $this->av;
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
    
    
    private function p10args($ac, $av) {
	switch($ac) {
	    case 1 : self::popTestArgs($av); // $av[1] = 1; $av[3] = 270836; $av[4] = 270827; $av[5] = 270836; $av[6] = '/tmp/access.log'; 
	    break;
	    case 7 : break;
	    default: kwas(0, 'bad # params'); break;
	}
	
	$this->av = $av;
    }
    
    private function popTestArgs(&$av) {
	$str = "blah 10196 0 270836 270768 270836 /tmp/access.log";
	$av  = explode(" ", $str);
	return;
    }
    
}

if (didCLICallMe(__FILE__)) new wsal_load_worker($argc, $argv);