<?php

require_once('/opt/kwynn/kwutils.php');

class load20_divide extends dao_generic_3 {
	
	const dropUntil = '2022-02-06 08:00';
	
	const lfin = '/tmp/a500l.log';
	const dbname = 'wsal20';
	const colla   = 'lines';
	
	function __construct() {

		$this->parentLevelDB();
	
		$sz = self::getFSZ(self::lfin);
		
		if (1) fork::dofork(true, 0, $sz - 1, ['load20_divide', 'doCh20'], self::lfin, self::dbname, self::colla);
		else {
			$rs = multi_core_ranges::get(0, $sz - 1);
			foreach($rs as $i => $r) $this->doCh20($r['l'], $r['h'], $i);		
		}
	}

	public static function doCh20($low, $high, $ri, $aargs) {
		
		$fnm = $aargs[0];
		$dbn = $aargs[1];
		$cnm = $aargs[2]; unset($aargs);
		
		$fts  = filemtime($fnm);
		$fmt = date('md-Hi-Y-s', $fts);
		$r = fopen($fnm, 'r'); unset($fnm);
		$iob = new inonebuf($dbn, $cnm); unset($dbn, $cnm);
		$i = -1;
		$p = $low;
		
		fseek($r, $low);
		
		while ($l = fgets($r)) {
			$pp = $p;
			$sll = strlen($l);
			$p += $sll;
			
			if ($i === -1) {
				$i++;
				if ($pp !== 0) continue;
			}
			
			++$i;
			
			$_id =  sprintf('%02d', $ri) . '-' . sprintf('%07d', $i) . '-' . $fmt;
		
			$t = [ '_id' => $_id, 'l' => $l, 'fp' => $pp, 'len' => $sll, 'fts' => $fts];
			try { $iob->ino($t); } catch (Exception $ex) {
				throw $ex;
			}

			if ($p > $high) break;
		}

		$toti = $iob->ino(false);
		
		return $toti; 		
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}
	
	private function parentLevelDB() {
		$dd = time() < strtotime(self::dropUntil);
		if (!$dd) return;

		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		$this->lcoll->drop();		
	}

}

new load20_divide();
