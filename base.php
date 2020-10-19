<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_load_and_parse {

    const lpath  = '/tmp/access.log';
    const lafter = '2020-10-15 20:16:00';
    const max2explines = 45;

    public function __construct() { $this->getLineOfDate();  }
    
    private function getLineOfDate() {
	
	static $tsa = false; if (!$tsa) $tsa = strtotime(self::lafter);
	
	$totn = intval(trim(shell_exec('wc -l < ' . self::lpath)));
	$this->grtotl = $nxt = $totn;
	
	$iminp = 0; self::avg($nxt, $iminp, $ignore); $imaxp = $totn - 1;
		
	for ($i=0; $i < self::max2explines; $i++) {
	    if (wsalParseOneLine($this->getLine($nxt), 1) >= $tsa) 
		 self::avg($nxt, $iminp, $imaxp);
	    else self::avg($nxt, $imaxp, $iminp);
	    if ($imaxp === $nxt) break;
	} 	
	
	return $nxt;
    }
    
    private static function avg   (&$a, $b, &$eref) { $eref = $a; $a = intval(ceil(($a + $b) / 2)); } 

    private function getLine($i) {
	$li = $this->grtotl - $i + 1;
	$c10 = "tail -n $li 2> /dev/null " . self::lpath . ' | head -n 1 ';
	return trim(shell_exec($c10));
    }
}

if (didCLICallMe(__FILE__)) new wsal_load_and_parse();