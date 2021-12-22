<?php

require_once('p10.php');
class agent_output {
	
	const dateF = 'Y/m/d H:i:s \U\TC P';
	const timeDiv = 1; // seconds or microseconds
	
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
		$dateSHu = self::usToHu($ma['mints']);
		$dateEHu = self::usToHu($ma['maxts']);
		$numLines	  = $ma[    'numLines'];
		$botNumLines  = $biga['bot_numLines'];
		$numLinesS	  = number_format($numLines);
		$botNumLinesS = number_format($botNumLines);
		$botPer = round(($botNumLines / $numLines) * 100) . '%';
		
		$daysf = (($ma['maxts'] - $ma['mints']) / (self::timeDiv * 86400));
		
		$days = intval(round($daysf)); 
		$lpdf  = $numLines / $daysf; unset($daysf);
		$lpd   = number_format(intval(round($lpdf))); unset($lpdf);
		
		unset($ma, $botNumLines);
		
		$bigATabHT = self::getBigTab($biga['agents'], $numLines); unset($biga, $numLines);
		
		require_once('template.php');
		

		return;
	}
	
	private static function getBigTab($a, $totn) {
		$ht = '';
		$i = 0;
		foreach($a as $r) { 
			$ht .=  '<tr>';
			$ht .=  '<td>';
			$ht .=    ++$i;
			$ht .= '</td>';
			$ht .=  '<td>';
			$ht .= number_format($r['count']);
			$ht .= '</td>';
			$ht .=  '<td>';
			$ht .= self::getPer($r['count'], $totn);
			$ht .= '</td>';
			$ht .=  '<td>';
			$ht .= $r['isbot'] ? 'Y' : '';
			$ht .= '</td>';
			$ht .=  '<td>';
			$ht .= $r['_id'];
			$ht .= '</td>';
			$ht .= '</tr>' . "\n";
		}
		return $ht;
	}
	
	private static function getPer($nr, $nt) {
		$p = intval(round(($nr / $nt) * 100));
		if ($p < 1) return '';
		return $p;
	}
	
	private static function usToHu($ts) {
		// $ts = intval(round($us / self::timeDiv));
		return date(self::dateF, $ts);
	}
}