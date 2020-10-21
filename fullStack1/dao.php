<?php

require_once('/opt/kwynn/kwutils.php');

class dao_wsal_anal extends dao_generic {
    const db = 'wsalogs';
      function __construct($erase = false) {
	    parent::__construct(self::db);
	    $this->lcoll    = $this->client->selectCollection(self::db, 'lines');
	    $this->a10coll  = $this->client->selectCollection(self::db, 'anal10');
	    if ($erase) $this->a10coll->drop();
	    $this->index();
      }

            
      public static function getDateRange() {
	 $o = new self();
	 return $o->getDateRangeI();
      }
      
      private function getDateRangeI() {
	 $ords = [1, -1]; 
	 foreach($ords as $ord) $res[] = $this->lcoll->findOne([], ['sort' => ['ts' => $ord], 'limit' => 1, 'projection' => ['_id' => 0, 'ts' => 1]]);
	 $ret['l'] = $res[0]['ts'];
	 $ret['h'] = $res[1]['ts'];
	  return $ret;
      }
      
      
      private function index() {
	  $this->a10coll->createIndex(['lmd5' => 1, 'n' => 1], ['unique' => true]);
	  $this->lcoll  ->createIndex(['ts'   => 1]);
      }
      
      public function putall($alldat) { $this->a10coll->insertMany($alldat); }
      
      public static function getAll() { 
	  $o = new self();
	  return $o->getAllI();
      }
      
      private function getAllI() { return $this->lcoll->find()->toArray(); }
      
      public function getjs($dl) {
	  $tsl = strtotime($dl);
	  $ra = [];
	  $a = $this->a10coll->find(['ts' => ['$gte' => $tsl], 'bot' => false, 'err' => 'OK', 'primeGet' => true ], ['sort' => ['n' => 1]]);
	 
	 foreach($a as $r) {
	    $ra[] = $r['js'];   
	 }
	 
	 return $ra;
	 
      }
}