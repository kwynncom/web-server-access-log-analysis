<?php

require_once('dao.php');
require_once('bots.php');

class dao_wsal_upgrades extends dao_wsal {
    
    public function __construct() {
	
	if (0) { // all updates made
	    parent::__construct(true);
	    $this->bot20();
	}
    }
    
    private function bot20() {
	$rs = $this->lcoll->find(['bot' => true, 'gold10' => true]);
	foreach($rs as $r) {
	    if (!isBot1210($r['agent'])) continue;
	    $this->lcoll->upsert(['_id' => $r['_id']], ['gold10' => false]);
	    
	    continue;
	}
    }
    
    private function bot10() {
	$rs = $this->lcoll->find(['bot' => false]);
	foreach($rs as $r) {
	    if (!isBot1210($r['agent'])) continue;
	    $this->lcoll->upsert(['_id' => $r['_id']], ['bot' => true]);
	    
	    continue;
	}
    }
}

if (didCLICallMe(__FILE__)) new dao_wsal_upgrades();