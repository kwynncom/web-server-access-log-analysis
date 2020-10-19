<?php

require_once('dao.php');

class find_rows extends dao_wsal {
  function __construct() {
	    parent::__construct(self::db);
      }   
      
      public function search() {
	    for ($i=1; $i <= 270836; $i++) {
		$res = $this->lcoll->findOne(['n' => $i]);
		if (!$res) {
		    echo $i . "\n";
		}
		
	    }
      }
}

if (didCLICallMe(__FILE__)) {
    $o = new find_rows();
    $o->search(); unset($o);
}