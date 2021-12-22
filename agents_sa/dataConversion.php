<?php

class dao_wsal_ua_conversion {
	
	const syncF = __DIR__ . '/../load/syncUA20.php';
	
	public static function doit() { new self(); }
	
	private function __construct() {
		$this->p10();
	}
	
	private function p10() {
		if (!file_exists(self::syncF)) return;
		require_once(self::syncF);
		dao_wsal_ua20::doit();
		
		
	}
}

