<?php

require_once('/opt/kwynn/kwutils.php');

class dao_wsal extends dao_generic {
    const db = 'wsalogs';
	function __construct() {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
      }

      public function putAll($allDat) {  $this->lcoll->insertMany($allDat);     }
}