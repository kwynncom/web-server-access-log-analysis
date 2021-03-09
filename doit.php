<?php

require_once('/opt/kwynn/kwutils.php');
require_once('bots.php');
require_once('parse.php');
require_once('agent.php');
require_once('dao.php');

class wsal_21_1 {
    
    public  function __construct() { 
	return; // turning off for now
	// $this->p10();  
	
    }
    public  function getA()	   { return $this->biga; }
    private static function gold10($a)	   { return !$a['bot'] && !$a['iref'] && !$a['err'] && !$a['xiref'];  }
    
    public static function lineToAnal($l) {
	$a = wsal_parse::parse($l);
	$a['line'] = $l;
	$a['bot'] = isBot1210($a['agent']);
	$a['iref'] = self::isIntRef($a['ref']);
	$a['xiref'] = ($a['iref'] && $a['ext'] === 'js') || self::f20($a);

	$a['err'] = $a['httpcode'] < 200 || $a['httpcode'] > 399;

	$a['gold10'] = self::gold10($a);	
	return $a;
    }   
    
    private function p10() {
	
	$dao = new dao_wsal();

	while ($linea = $dao->get()) {
	    $line = $linea['line'];

	    $a = array_merge(wsal_parse::parse($line), $linea);

	    // moved
	    $a['i'] = $i = $linea['i'];
	    
	    // $dao->put($a);
	
	    $this->out($a, $i);
	}
	return;
    }

    private static function filterJS($a) {
	static $js = [];
	if (!$js) $js = ['date', 'agent', 'bot', 'url', 'iref', 'ip', 'i', 'xiref', 'err', 'ref', 'gold10'];
	foreach($a as $f => $ignore) if (!in_array($f, $js)) {
	    unset($a[$f]);
	}
	return $a;
    }
    
    private static function f20($a) {
	static $xfiles = false; 
	if (!$xfiles) $xfiles = ['/t/1/09/counter/c_img/Kwynn_counter_screenshot_2012_0614_2055.gif', '/valid-xhtml10.png', '/t/0/other/valid-xhtml10.png', 
	    '/t/5/02/html5_valid.jpg', '/favicon.ico'];
	if (in_array($a['url'], $xfiles)) return true;
	return false;
    }
   
    public static function isIntRef($rin) { return preg_match('/^https?:\/\/w?w?w?\.?kwynn\.com/', $rin, $ms) ? true : false; }
    
    private function out($a, $i) {
	
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
	

	return;	
    }
    

    
    private static function cmd($c) { $c = str_replace('GET ', '', $c); return $c;     }
    
    private static function ex($a, $i) { if ($i === 670) {
	kwynn(); 
	
    } 
	
    }
}

if (iscli()) new wsal_21_1();