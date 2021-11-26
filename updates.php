<?php

require_once('dao.php');
require_once('bots.php');

class dao_wsal_upgrades extends dao_wsal {
    
    public function __construct() {
	
	if (1) { // all updates made
	    parent::__construct(true);
	    $this->bot20();
	}
    }
    
    private function bot20() {
	$rs = $this->lcoll->find(['bot' => false], ['sort' => ['_id' => -1]]);
	foreach($rs as $r) {
	    if ($r['i'] === 131001) {
		kwynn();
	    }
	    if (!isBot($r['agent'])) continue;
	    $res = $this->lcoll->upsert(['_id' => $r['_id']], ['gold10' => false, 'bot' => true]);
	    continue;
	}
    }
    
    private function bot10() {
	$rs = $this->lcoll->find(['bot' => false]);
	foreach($rs as $r) {
	    if (!isBot($r['agent'])) continue;
	    $this->lcoll->upsert(['_id' => $r['_id']], ['bot' => true]);
	    
	    continue;
	}
    }
}

if (didCLICallMe(__FILE__)) new dao_wsal_upgrades();