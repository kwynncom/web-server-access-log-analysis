<?php

require_once('/opt/kwynn/kwutils.php');
require_once('./../agent.php');

class agent_to_web {
    const fdir  = '/tmp/';
    const fname = 'kwynn_com_ua_counts.json';
    const path  = self::fdir . self::fname;    
    const site  = 'kwynn.com';
    const aggLabel = self::site . ' web server access logs user agent counts';
    const dateF = 'Y/m/d H:i:s \U\TC P';
    
    public $allin;
    
    public function __construct() {
	$this->pop10();
	$this->allin['human_read'] = $this->p10();
    }
    
    private function pop10() {
	$o = new wsla_agent_p30('desc');
	$this->allin = $o->getAll();
    }
    
    private function p10() {
	$a = $this->allin['meta'];
	
	$r = [];
	
	foreach(['minDate', 'maxDate'] as $k) $r[$k] = date(self::dateF, $a[$k]);
	$r['lines']    = number_format($a['count']);
	$r['linesBot'] = number_format($a['countBots']);
	$r['botp']     = round(($a['countBots'] / $a['count']) * 100) . '%';
	$d =  ($a['maxDate'] - $a['minDate']) / 86400;
	// $ds = sprintf('%0.3f', $d);
	$ds = intval(round($d));
	$r['days' ] = $ds;
	$lpd = $a['count'] / $d;
	$lpdf = intval(round($lpd));
	$r['lpd'] = $lpdf;
	
	return $r;
	
    }
    
    public static function getJSON() {
	$o = new self();
	return json_encode($o->get());
    }
    
    public function get() { return $this->allin; }
}

if (didCLICallMe(__FILE__)) { new agent_to_web(); }