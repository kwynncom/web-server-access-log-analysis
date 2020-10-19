<?php

require_once('/opt/kwynn/kwutils.php');

class dao_wsal_anal extends dao_generic {
    const db = 'wsalogs';
      function __construct() {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
	    $this->a10coll  = $this->client->selectCollection(self::db, 'anal10');
	    $this->index();
      }

      private function index() {
	  $this->a10coll->createIndex(['lmd5' => 1, 'n' => 1], ['unique' => true]);
      }
      
      public function putall($alldat) { $this->a10coll->insertMany($alldat); }
      
      public static function getAll() { 
	  $o = new self();
	  return $o->getAllI();
      }
      
      private function getAllI() { return $this->lcoll->find()->toArray(); }
}