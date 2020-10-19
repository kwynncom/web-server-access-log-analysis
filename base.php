<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');

class wsal_load_and_parse {

    const alpath  = '/tmp/access.log';
    const linesAfter = '2020-10-15 19:30:00';
    const maxLines2PowOf = 45;

    public function __construct() { 
	$this->setTotLines();
	$this->getLineOfDate(strtotime(self::linesAfter)); 
    }
    
    private function setTotLines() { $this->grtotl = intval(trim(shell_exec('wc -l < ' . self::alpath))); }
    
    private function getLineOfDate($tsa) {
	$nxt = $imaxp = $this->grtotl;
	$iminp = 0; self::avg($nxt, $iminp, $ignore); unset($ignore);
	for ($i=0; $i < self::maxLines2PowOf && $nxt < $imaxp; $i++) 
	    if (wsalParseOneLine($this->getLine($nxt)) >= $tsa) self::avg($nxt, $iminp, $imaxp);
	    else						self::avg($nxt, $imaxp, $iminp);
	return $nxt;
    }
    
    private function avg    (&$a, $b, &$eref) { $eref = $a; $a = intval(ceil(($a + $b) / 2)); } 
    private function getLine($i) { return trim(shell_exec('tail -n ' . ($this->grtotl - $i + 1) . ' 2> /dev/null ' . self::alpath . ' | head -n 1 ')); }
}

if (didCLICallMe(__FILE__)) new wsal_load_and_parse();