<?php

require_once('config.php');

class wsal_load_live {
	public function __construct() {
		new wsal_load_lock();
	}
	
}

if (didCLICallMe(__FILE__)) new wsal_load_live();
