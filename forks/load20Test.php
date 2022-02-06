<?php

require_once('/opt/kwynn/kwutils.php');
require_once('ranges.php');

class load20_divide extends dao_generic_3 {
	
	// const lfin = '/tmp/access.log';
	// const lfin = '/tmp/a91.log';
	
	const lfin = '/tmp/a6.log';
	
	function __construct() {
		parent::__construct('wsal20');
		$this->creTabs(['l' => 'lines']);
		if (time() < strtotime('2022-02-06 04:00')) $this->lcoll->drop();
		kwas(is_readable(self::lfin), 'file not readable');
		$this->fmt = date('md-Hi-Y-s', filemtime(self::lfin));
		$this->sz = $sz = $this->thesz = filesize(self::lfin);
		$rs = multi_core_ranges::get(0, $sz - 1);
		$this->r = fopen(self::lfin, 'r');
		
		foreach($rs as $i => $r) {
			$this->doCh20($r, $i);
		}
	}
	
	function doCh20($r, $ri) {
		extract($r); unset($r);
		$low = $l; unset($l);
		
		fseek($this->r, $low);
		
		$i = -1;
		$p = $low;
		while ($l = fgets($this->r)) {
			if ($i === -1) {
				$i++;
				if ($p !== 0) continue;
			}
			++$i;
			$sll = strlen($l);
			$t = ['l' => $l, 'cn' => $i, '_id' => sprintf('%02d', $ri) . '-' . sprintf('%07d', $i) . '-' . $this->fmt, 'rn' => $ri + 1, 'len' => $sll];
			try { inonebuf($t, $this->lcoll); } catch (Exception $ex) {
				throw $ex;
			}
	
			$p += $sll;
			if ($p > $h) break;
		}

		$toti = inonebuf(false, $this->lcoll);
		
		return $toti; 		
	}

}

new load20_divide();
