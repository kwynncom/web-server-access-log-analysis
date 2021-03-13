<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/mongodb2.php');

class dao_myip extends dao_generic_2 {
    const dbName = 'myip';
    const tmarg = 86400;
    
    public function __construct() {
	parent::__construct(self::dbName, __FILE__);    
	$this->creTabs(['i' => 'ips']);
	$res = $this->icoll->createIndex(['ip' => 1, 'agent' => 1], ['unique' => true]);
	return;
    }
    
    public function put($dat) {
	$q = ['ip' => $dat['ip'], 'agent' => $dat['agent']];
	if ($this->icoll->findOne($q)) {
	    $tru = $this->icoll->upsert($q, ['from' => $dat['from'], 'to' => $dat['to']]);
	    return;
	}
	$dat['_id'] = $this->icoll->getseq2('idoas', $dat['to']);
	$this->icoll->insertOne($dat);
	return;
    }
    
    public function getLatest() {
	$res = 
	$this->icoll->aggregate(
	[
	  [
	    '$group' =>
	    [
	      '_id' => 'maxTo',
	      'latest' =>  [ '$max' => '$to' ],
	    ]
	 ]
	 ]
	)->toArray();	
	
	if (!isset($res[0]['latest'])) return false;
	return     $res[0]['latest'];
    }
    
    public function getall() { return $this->icoll->find(); }
    
    public function ismy($ip, $agent, $ts) {

	static $a  = false;
	
	if (!$a) {
	    $allin = $this->getall();
	    $a  = self::getBigA($allin); unset($allin);
	}
	
	if (!isset($a[$ip][$agent])) return false;
	$ta       = $a[$ip][$agent];
	$t = $ta['to'];
	$f = $ta['from'];
	if ($ts <= $t + self::tmarg && $ts >= $f - self::tmarg) return true;
	return false;
	
	
	

	
    }
    
    private static function getBigA($allin) {
	$a = [];	
	foreach($allin as $r) {
	
	    $a[$r['ip']][$r['agent']]['from'] = $r['from'];
	    $a[$r['ip']][$r['agent']]['to'] = $r['to'];  
	}
	
	return $a;
    }
}


class dao_qemail extends dao_generic_2 {
    const dbName = 'qemail';
    const emailf = '/var/kwynn_www/myemail1.txt';
    const goBackS = 86400 * 30;
    const goBackReset = false;

    public function __construct() {
	parent::__construct(self::dbName, __FILE__, true);
	$this->creTabs(['u' => 'usage']);
	$this->ipdb = new dao_myip();
	$r1 = $this->load10();
	$this->load20($r1);
    }
    
    private function load20($ain) {
	
	foreach($ain as $r10) {
	    $r = $r10['_id'];
	    $t = array_merge($r, $r10); unset($t['_id']);
	    $this->ipdb->put($t);
	}


	
    }
    
    private function getMyEmail() {
	$f = self::emailf; kwas(file_exists($f), 'my email file not exists 1018');
	$e = trim(file_get_contents($f)); kwas($e, 'no email - my email 1018');
	return $e;
	
	
    }
    
    private function load10() {
	
	$now = time();
	
	$sinceldb = $this->ipdb->getLatest();
	if (!$sinceldb || (( $now - $sinceldb < self::goBackS) && self::goBackReset)) $since = time() - self::goBackS;
	else $since = $sinceldb; unset($sinceldb);
		
	$email = $this->getMyEmail();

	
	$res = 
	$this->ucoll->aggregate(
	[
	[   '$match' => [
		'timed.time' => ['$gte' => $since],
		'email' => $email
	    ]],
	  [
	    '$group' =>
	    [
	      '_id' =>  [ 'agent' => '$agent', 'ip' => '$ip'],
	      'from' =>  [ '$min' => '$timed.time' ],
		'to' => [ '$max' => '$timed.time' ],      
	    ]
	 ]
	 ]
	)->toArray();
	
	return $res;
    }
}

if (didCLICallMe(__FILE__)) new dao_qemail();