<?php

// require_once('dao_generic.php');
require_once('/opt/kwynn/kwutils.php');

class dao_wsal extends dao_generic_3 {
	const dbname = 'wsal';
	
	public function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines']);
	}
}