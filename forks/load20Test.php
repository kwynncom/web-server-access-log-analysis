<?php

require_once('/opt/kwynn/kwutils.php');

class load20_divide extends dao_generic_3 {
	
	const lfin = '/tmp/a400.log';
	const dbname = 'wsal20';
	const colla   = ['l' => 'lines'];
	
	function __construct() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		if (time() < strtotime('2022-02-06 04:00')) $this->lcoll->drop();
		$this->lcoll->createIndex(['md5' => 1], ['unique' => true]);
		
		kwas(is_readable(self::lfin), 'file not readable');
		$this->sz = $sz = $this->thesz = filesize(self::lfin);
	
		$rs = multi_core_ranges::get(0, $sz - 1);
		if (1) fork::dofork(['load20_divide', 'doCh20'], 0, $sz - 1);
		else foreach($rs as $i => $r) { 	$this->doCh20($r['l'], $r['h'], $i);		}
		
	}

	public static function doCh20($low, $high, $ri) {
		
		$fmt = date('md-Hi-Y-s', filemtime(self::lfin));
		$r = fopen(self::lfin, 'r');
		
		
		$iob = new inonebuf(self::dbname, self::colla[key(self::colla)]);
		$fmt = date('md-Hi-Y-s', filemtime(self::lfin));	
		
		fseek($r, $low);
		
		$i = -1;
		$p = $low;
		while ($l = fgets($r)) {
			$sll = strlen($l);
			$p += $sll;
			
			if ($i === -1) {
				$i++;
				if ($ri !== 0) continue;
			}
			
			++$i;
			
			$md5 = md5($l . $i . $ri);
		
			$t = ['l' => $l, 'cn' => $i, '_id' => sprintf('%02d', $ri) . '-' . sprintf('%07d', $i) . '-' . $fmt, 'rn' => $ri + 1, 'len' => $sll, 'md5' => $md5];
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
