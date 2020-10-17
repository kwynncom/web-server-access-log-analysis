<?php

class dao_wsal extends dao_generic {
    const db = 'wsalogs';
	function __construct() {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
      }
      
      
}