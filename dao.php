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
	$this->dicks(); // data integrity checks.  Not trying to be vulgar.  :)
   }
   
   private function dicks() { // data integrity checks.  Not trying to be vulgar.  :)
       kwas(!$this->lcoll->find(['line' => '/^\s*$/']), 'should not have blank lines');
       $nr = $this->lcoll->find(['datv' => ['$ne' => self::datv]]);
       kwas(!$nr, 'not all datv current');

       
       $cnt = $this->lcoll->count();
       if ($cnt === 0) return;
       
       kwas($this->lcoll->count(['i' => 1]	 ) === 1  , 'no count of 1');
       kwas($this->lcoll->count(['i' => $cnt]	 ) === 1, 'no count of n');
       kwas($this->lcoll->count(['i' => $cnt + 1]) === 0, '');
       
       return;
   }
   
   public function get() { 
       static $i = 0;
       
       return $this->lcoll->findOne(['i' => ++$i]);  
       
   }
    
}