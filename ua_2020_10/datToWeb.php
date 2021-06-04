<?php

require_once('/opt/kwynn/kwutils.php');

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
	if (isAWS()) $path = __DIR__ . '/' . self::fname;
	else         $path = self::path;
	$this->allin = json_decode(self::safeLoad($path), 1);	
    }
    
    private static function safeLoad($path) {
	if (file_exists($path)) $s = $path;
	else {
	    $t = '/tmp/' . self::fname;
	    if (file_exists($t)) $s = $t; 
	}
	return file_get_contents($s);
    }
  
    private function p10() {
	$a = $this->allin['meta'];
	
	$r = [];
	
	foreach(['minDate', 'maxDate'] as $k) $r[$k] = date(self::dateF, $a[$k]);
	$r['lines'] = number_format($a['lines']);
	$d =  ($a['maxDate'] - $a['minDate']) / 86400;
	// $ds = sprintf('%0.3f', $d);
	$ds = intval(round($d));
	$r['days' ] = $ds;
	$lpd = $a['lines'] / $d;
	$lpdf = intval(round($lpd));
	$r['lpd'] = $lpdf;
	
	return $r;
	
    }
    
    public static function getJSON() {
	$o = new self();
	return json_encode($o->allin);
    }
    
    
    public static function save($tota, $agga) {
	$fa = [];
	$fa['meta'] = $tota;
	$fa['user_agents' ] = $agga;	
	$json = json_encode($fa);
	$path = self::path;
	kwas(file_put_contents($path, $json), 'save fail ua counts');
	echo("saved to $path\n");	
    }
}

if (didCLICallMe(__FILE__)) { agent_to_web::get(); }