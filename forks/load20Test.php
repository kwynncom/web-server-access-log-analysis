<?php

require_once('/opt/kwynn/kwutils.php');

class load20_divide extends dao_generic_3 {
	
	const lfin = '/tmp/a500l.log';
	const dbname = 'wsal20';
	const colla   = ['l' => 'lines'];
	
	function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		if (time() < strtotime('2022-02-06 04:00')) $this->lcoll->drop();
		
		kwas(is_readable(self::lfin), 'file not readable');
		$this->sz = $sz = $this->thesz = filesize(self::lfin);
	
		$rs = multi_core_ranges::get(0, $sz - 1);
		if (1) fork::dofork(['load20_divide', 'doCh20'], 0, $sz - 1);
		else foreach($rs as $i => $r) { 	$this->doCh20($r['l'], $r['h'], $i);		}
		
	}

	public static function doCh20($low, $high, $ri) {
		
		$fts  = filemtime(self::lfin);
		$fmt = date('md-Hi-Y-s', $fts);
		$r = fopen(self::lfin, 'r');
		
		
		$iob = new inonebuf(self::dbname, self::colla[key(self::colla)]);
		$fmt = date('md-Hi-Y-s', filemtime(self::lfin));	
		
		fseek($r, $low);
		
		$i = -1;
		$p = $low;
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

}

new load20_divide();
