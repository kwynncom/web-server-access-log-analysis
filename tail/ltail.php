<?php

require_once('/opt/kwynn/kwutils.php');
require_once('bots1210.php');
require_once(__DIR__ . '/../cli10/parse.php');

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
	    if (isBot1210($a['agent'])) continue;
	    if (self::isIntRef($a['ref'])) continue;

	    $code = $a['httpcode'];
	    if ($code < 200 || $code > 399) continue;
	    
	    
	    
	    echo $line . "\n";
	}
	
	
	return;
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