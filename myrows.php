<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/mongodb2.php');

class dao_myip extends dao_generic_2 {
    const dbName = 'myip';    
    
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
	$dat['_id'] = $this->icoll->getseq2('idoas');
	$this->icoll->insertOne($q, $dat);
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
	
	$a = [];
	foreach($ain as $r10) {
	    $r = $r10['_id'];
	    $t = array_merge($r, $r10); unset($t['_id']);
	    $this->ipdb->put($t);
	    $a[$r['ip']][$r['agent']]['from'] = $r10['from'];
	    $a[$r['ip']][$r['agent']]['to'] = $r10['to'];    
	}
	
	$this->biga = $a;
	
    }
    
    private function getMyEmail() {
	$f = self::emailf; kwas(file_exists($f), 'my email file not exists 1018');
	$e = trim(file_get_contents($f)); kwas($e, 'no email - my email 1018');
	return $e;
	
	
    }
    
    private function load10() {
	
	$now = time();
	
	$sinceldb = $this->ipdb->getLatest();
	if ((!$sinceldb || $now - $sinceldb < self::goBackS) && self::goBackReset) $since = time() - self::goBackS;
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

new dao_qemail();