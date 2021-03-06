<?php

class dao_wsal extends dao_generic {
    const db = 'wsalogs';
	function __construct() {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
      }
      
      private function dropAndI() {
	  $this->lcoll->drop();
	  $this->lcoll->createIndex(['lmd5' => 1, 'n' => 1], ['unique' => true]);
	  // $this->
      }
      
      public function put($dat) {
	  if (!isset($this->cleaned)) {
	      $this->dropAndI();
	      $this->cleaned = true;
	  }
	  
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