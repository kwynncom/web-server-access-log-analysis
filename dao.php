<?php

require_once('/opt/kwynn/mongodb2.php');
require_once('doit.php');

class dao_wsal extends dao_generic_2 {

    const dbName = 'wsalogs';
    const datv = 2;
    
    public function __construct() {
	parent::__construct(self::dbName, __FILE__);
	$this->creTabs(['l' => 'lines']);
	$thei = $this->lcoll->createIndex(['md5' => 1, 'i' => 1], ['unique' => true]);
	$this->dicks(); // data integrity checks.  Not trying to curse.  :)
	$this->i = 1;
   }
   
   private function dicks() {
       kwas(!$this->lcoll->find(['line' => '/^\s*$/']), 'should not have blank lines');
       $nr = $this->lcoll->find(['datv' => ['$ne' => self::datv]]);
       foreach($nr as $r) {
	   $r = array_merge($r, wsal_21_1::lineToAnal($r['line']));
	   continue;
       }
       return;
   }
   
   public function get() { return $this->lcoll->findOne(['i' => $this->i++]);  }
   
   public function put($d) {
       $this->lcoll->upsert(['_id' => $d['_id']], $d);
       
   }
    
}