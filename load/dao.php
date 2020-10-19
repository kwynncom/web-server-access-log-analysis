<?php

require_once('/opt/kwynn/kwutils.php');

class dao_wsal extends dao_generic {
    const db = 'wsalogs';
	function __construct() {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
	    $this->indexn();
      }
      
      private function indexn() {
	  // $this->lcoll->createIndex(['n' => 1]);	  
      }
      
      
      private function dropAndI() {
	  $this->lcoll->drop();
	  $this->lcoll->createIndex(['lmd5' => 1, 'n' => 1], ['unique' => true]);
	  // $this->
      }
      
      public static function clean() {
	 $o = new self();
	 $o->dropAndI();
      }
      
      public function putAll($allDat) {
	  $this->lcoll->insertMany($allDat);
      }
      
      public function put($dat) {
  
	  $this->lcoll->upsert(['lmd5' => $dat['lmd5'], 'n' => $dat['n']], $dat);

	  return;
	 // $this->  
      }
      
      public function jsget() {
	  $ra = [];
	  $a = $this->lcoll->find([], ['sort' => ['n' => 1]]);
	 
	 foreach($a as $r) {
	    $ra[] = $r['js'];   
	 }
	 
	 return $ra;
	 
      }
      
}