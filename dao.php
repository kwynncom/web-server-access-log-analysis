<?php

require_once('/opt/kwynn/mongodb2.php');
require_once('doit.php');
// require_once('updates.php');

class dao_wsal extends dao_generic_2 {

    const dbName = 'wsalogs';
    const datv = 5;
    
    public function __construct($fromChild = false) {
	parent::__construct(self::dbName, __FILE__);
	$this->creTabs(['l' => 'lines']);
	$thei = $this->lcoll->createIndex(['md5' => 1, 'i' => 1], ['unique' => true]);
	if (!$fromChild) {
//	    new dao_wsal_upgrades();
	    $this->dicks(); // data integrity checks.  Not trying to be vulgar.  :)
	}
   }
   
   private function rerun3() {
	$all = $this->lcoll->find(['datv' => ['$ne' => self::datv]]);
	foreach($all as $r) {
	    $r = wsal_21_1::addAnal($r);
	    $r['datv'] = self::datv;
	    $res = $this->lcoll->upsert(['_id' => $r['_id']], $r);
	    continue;
	}
   }
   
   private function dicks() { // data integrity checks.  Not trying to be vulgar.  :)
       kwas(!$this->lcoll->find(['line' => '/^\s*$/']), 'should not have blank lines');
       
       $this->rerun3();
       
       $nr = $this->lcoll->find(['datv' => ['$ne' => self::datv]]);
       kwas(!$nr, 'not all datv current');

       
       $cnt = $this->lcoll->count();
       if ($cnt === 0) return;
       
       kwas($this->lcoll->count(['i' => 1]	 ) === 1  , 'no count of 1');
       kwas($this->lcoll->count(['i' => $cnt]	 ) === 1, 'no count of n');
       kwas($this->lcoll->count(['i' => $cnt + 1]) === 0, '');
       
       return;
   }
   
   public function exists($q) { return $this->lcoll->count($q);  }
   
   public function insertMany($q) { $this->lcoll->insertMany($q); }
   
   public function get($lowest, $rcnt) { 
       // 2021/05/24 - adding bot false because otherwise may not see anything
       return $this->lcoll->find(['i' => ['$lt' => $lowest], 'bot' => false], ['sort' => ['i' => -1], 'limit' => $rcnt]);  
   }
    
}

if (didCLICallMe(__FILE__)) new dao_wsal();