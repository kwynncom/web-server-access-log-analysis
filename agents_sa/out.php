<?php

require_once('p10.php');
class agent_output {
	
	const dateF = 'Y/m/d H:i:s \U\TC P';
	
	public function __construct() { 
		$this->p10();
	}
	
	private function p10() {
		$biga = get_uagents::get();
		$ma   = $biga['meta'];
		$dateSHu = self::usToHu($ma['mintsus']);
		$dateEHu = self::usToHu($ma['maxtsus']);
		unset($ma, $biga);
		
		require_once('template.php');
		

		return;
	}
	
	private static function usToHu($us) {
		$ts = intval(round($us / M_MILLION));
		return date(self::dateF, $ts);
		
		
		
	}
}