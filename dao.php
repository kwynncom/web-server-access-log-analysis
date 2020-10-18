<?php

class dao_wsal extends dao_generic {
    const db = 'wsalogs';
	function __construct() {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
      }
      
      public function put($dat) {
	  if (!isset($this->cleaned)) {
	      $this->lcoll->drop();
	      $this->cleaned = true;
	  }
	  $this->lcoll->insertOne($dat);
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