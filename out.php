<?php

require_once('agent.php');

class wsal_cli_out10 {
    
    
    public static function out($a) {
	
	static $i = 1;
	
	
	$s  = '';
	$s .= date('m/d H:i:s', $a['ts']);
	$s .= ' ';
	$s .= self::cmd($a['cmd']);

	self::ex($a, $i);
	
	$s .= ' ';
	$s .= wsla_agent_p30::get($a['agent']);
	
	echo($i++ . ' ' . $s . "\n");
	return;	
    }
    
    private static function cmd($c) {
	$c = str_replace('GET ', '', $c);
	return $c;
    }
    
    private static function ex($a, $i) {
	if ($i === 752) {
	    kwynn();
	}
	
    }
}