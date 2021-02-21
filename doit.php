<?php

require_once('/opt/kwynn/kwutils.php');
require_once('bots.php');
require_once('parse.php');
require_once('agent.php');

class wsal_21_1 {
    
    const dfile = '/tmp/log/l10k.log';
    
    public function __construct() {
	$this->doargs10();
	$this->p10();
    }
    
    public function getA() { return $this->biga; }
    
    private function p10() {
	if (!isset($this->infilename)) return;
	$fh = fopen($this->infilename, 'r');
	while ($line = fgets($fh)) {
	    $a = wsal_parse::parse($line);
	    $a['bot'] = isBot1210($a['agent']);
	    if (self::f10($a)) continue;
	    $this->out($a);
	}
	fclose($fh);
	return;
    }
    
    private static function f10($a) {
	static $ffs10 = false;
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
	if ($argv) foreach($argv as $a) if (substr($a, 0, strlen($fk)) === $fk) $this->infilename = substr($a, strlen($fk));	
	if (!isset($this->infilename)) $this->infilename = self::dfile;
    }
    
    public static function isIntRef($rin) { return preg_match('/^https?:\/\/w?w?w?\.?kwynn\.com/', $rin, $ms); }
    
    private function out($a) {
	static $i = 1;
	$s  = '';
	$s .= $a['date'] = date('m/d H:i:s', $a['ts']);
	$s .= ' ';
	$s .= $a['cmd'] = self::cmd($a['cmd']);

	self::ex($a, $i);
	
	$s .= ' ';
	$s .= $a['agent'] = wsla_agent_p30::get($a['agent']);
	
	if (iscli() && 1) echo($i . ' ' . $s . "\n");
	
	if (!iscli() || 1) {
	    if (!isset($this->biga)) $this->biga = [];
	    $this->biga[] = self::filterJS($a);
	}
	
	$i++;
	return;	
    }
    
    private static function filterJS($a) {
	static $js = [];
	if (!$js) $js = ['date', 'agent', 'bot', 'url'];
	foreach($a as $f => $ignore) if (!in_array($f, $js)) unset($a[$f]);
	return $a;
    }
    
    private static function cmd($c) { $c = str_replace('GET ', '', $c); return $c;     }
    
    private static function ex($a, $i) { if ($i === 752) { kwynn(); } }
}

if (iscli()) new wsal_21_1();