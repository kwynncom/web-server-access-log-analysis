<?php

require_once('/opt/kwynn/kwutils.php');
require_once('./load/parse.php');

class wsal_ua20 extends dao_generic {
    const db = 'wsalogs';
    function __construct($erase = false) {
	  parent::__construct(self::db);
	  $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
	  $this->p10();
    }
    
    private function p10() {
	$res = $this->lcoll->find(['agent' => ['$regex' => '']], ['limit' => 100])->toArray();
	$res = $res[0];
	wsalParseOneLine($res['rline'], 0);
	
	
	
	
	echo (count($res));
    }
}

if (didCLICallMe(__FILE__)) new wsal_ua20();
