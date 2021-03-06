<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/../cli/' . 'parse.php');

class wsalDateFilter {

    const maxLines2PowOf = 45;

    public static function get($path, $date) { $o = new self($path, $date); return $o->getI();   }
    
    private function getI() { return ['tot' => $this->grtotl, 'start' => $this->start]; }
    
    private function __construct($path, $date) { 
	$this->grtotl = intval(trim(shell_exec('wc -l < ' . $path))); 
	$this->lpath  = $path;
	$this->lats = strtotime($date);
	$this->getLineOfDate(); 
    }

    private function getLineOfDate() {
	$nxt = $imaxp = $this->grtotl;  $iminp = 0; self::avg($nxt, $iminp, $ignore); unset($ignore);
	for ($i=0; $i < self::maxLines2PowOf && $nxt < $imaxp; $i++) 
	    if (wsal_parse::parse($this->getLine($nxt), true) >= $this->lats) self::avg($nxt, $iminp, $imaxp);
	    else						self::avg($nxt, $imaxp, $iminp);
	$this->start = $nxt;
    }
    
    private function avg    (&$a, $b, &$eref) { $eref = $a; $a = intval(ceil(($a + $b) / 2)); } 
    private function getLine($i) { return trim(shell_exec('tail -n ' . ($this->grtotl - $i + 1) . ' 2> /dev/null ' . $this->lpath . ' | head -n 1 ')); }
}
