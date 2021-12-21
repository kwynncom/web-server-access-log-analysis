<?php

require_once('p10.php');
class agent_output {
	
	const dateF = 'Y/m/d H:i:s \U\TC P';
	
	public function __construct() {
		$this->jsonOnly();
		$this->p10();
	}
	
	private function jsonOnly() {
		if (!isrv('json')) return;
		$j = get_uagents::getJSON();
		kwjae($j, true);
	}
	
	private function p10() {
		$biga = get_uagents::get();
		$ma   = $biga['meta'];
		$dateSHu = self::usToHu($ma['mintsus']);
		$dateEHu = self::usToHu($ma['maxtsus']);
		$numLines	  = $ma[    'numLines'];
		$botNumLines  = $biga['bot_numLines'];
		$numLinesS	  = number_format($numLines);
		$botNumLinesS = number_format($botNumLines);
		$botPer = round(($botNumLines / $numLines) * 100) . '%';
		
		$daysf = (($ma['maxtsus'] - $ma['mintsus']) / (M_MILLION * 86400));
		
		$days = intval(round($daysf)); 
		$lpdf  = $numLines / $daysf; unset($daysf);
		$lpd   = number_format(intval(round($lpdf))); unset($lpdf);
		
		unset($ma, $biga, $botNumLines, $numLines);
		require_once('template.php');
		

		return;
	}
	
	private static function usToHu($us) {
		$ts = intval(round($us / M_MILLION));
		return date(self::dateF, $ts);
	}
}