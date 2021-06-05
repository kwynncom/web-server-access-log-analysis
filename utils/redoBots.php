<?php

require_once(__DIR__ . '/../dao.php');

class redoBots extends dao_wsal {
    
    const botfile = __DIR__ . '/../bots.php';
    
    public function __construct() {
	parent::__construct('redo');
	$this->creTabs(['b' => 'bottime']);
    }
    
    public static function doit() {
	$o = new self();
	return $o->doitpr();
    }
    
    private function doitpr() {
	$cts = filemtime(self::botfile);
	$res = $this->bcoll->findOne();
	if (!$res || $res['botts'] !== $cts) {
	    $dat = ['botts' => $cts];
	    $this->bcoll->deleteMany([]);
	    $this->bcoll->insertOne($dat);
	    return true;
	}
	
	return false;
	
    }
}