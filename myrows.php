<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/mongodb2.php');

class dao_qemail extends dao_generic_2 {
    const dbName = 'qemail';
    const emailf = '/var/kwynn_www/myemail1.txt';
    const goBackS = 86400 * 7;

    public function __construct() {
	parent::__construct(self::dbName, __FILE__, true);
	$this->creTabs(['u' => 'usage']);
	$this->load10();
    }
    
    private function getMyEmail() {
	$f = self::emailf; kwas(file_exists($f), 'my email file not exists 1018');
	$e = trim(file_get_contents($f)); kwas($e, 'no email - my email 1018');
	return $e;
	
	
    }
    
    private function load10() {
	
	$email = $this->getMyEmail();
	
	$res = 
	$this->ucoll->aggregate(
	[
	[   '$match' => [
		'timed.time' => ['$gte' => time() - self::goBackS],
		'email' => $email
	    ]],
	  [
	    '$group' =>
	    [
	      '_id' =>  [ 'agent' => '$agent', 'ip' => '$ip', 'email' => '$email'],
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