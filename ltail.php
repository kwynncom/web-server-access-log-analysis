<?php

require_once('/opt/kwynn/kwutils.php');
require_once('bots1210.php');
require_once(__DIR__ . '/parse.php');
require_once('out.php');

class wsal_tail {
    public function __construct() {
	$this->doargs10();
	$this->p10();
    }
    
    private function p10() {
	if (!isset($this->infilename)) return;
	
	$t = shell_exec('cat ' . $this->infilename);
	$nextline = strtok($t, "\n");

	while ($nextline) {
	    $line = $nextline;
	    $nextline = strtok("\n");
	    $a = wsal_parse::parse($line);
	    if (self::f10($a)) continue;
	    wsal_cli_out10::out($a);
	}
	
	
	return;
    }
    
    private static function f10($a) {
	
	static $ffs10 = false;
	
	if (isBot1210($a['agent'])) return true;
	if (self::isIntRef($a['ref'])) return true;

	$code = $a['httpcode'];
	if ($code < 200 || $code > 399) return true;	
	
	
	if (!$ffs10) $ffs10 = ['gif', 'png', 'ico', 'js', 'jpg', 'pdf'];
	if (in_array($a['ext'], $ffs10)) return true;
	
	return false;
    }
    
    
    
    private function doargs10() {
	global $argv;
	
	static $fk = '-file=';
	
	foreach($argv as $a) {
	    if (substr($a, 0, strlen($fk)) === $fk) $this->infilename = substr($a, strlen($fk));
	}
    }
    
    private static function isIntRef($rin) {
	$isint = preg_match('/^https?:\/\/w?w?w?\.?kwynn\.com/', $rin, $ms);
	if ($isint) {
	    kwynn();
	} else {
	    kwynn();
	}
	return $isint;
    }
    
}

new wsal_tail();