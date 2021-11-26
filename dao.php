<?php

require_once('dao_generic.php');

class dao_wsal extends dao_generic_3 {
	
	const dbname = 'wsal';
	
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines']);
		if (!isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'linen' => 1], ['unique' => true]); // 408 lines can be in the same microsecond
		$this->lcoll->createIndex(['fmd5' => -1, 'linen' => 1], ['unique' => true]);
	}
	
	public function putAllLines($all) { $this->lcoll->insertMany($all); }
	
	
}
