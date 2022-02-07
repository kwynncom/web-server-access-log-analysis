<?php

require_once(__DIR__ . '/../load/parse.php');

class log_load_worker {
	public static function doit(...$args) {
		new self($args);
	}

	private function __construct($a5a) {
		$this->set10($a5a);
		if (1) $this->do40 ($a5a);
		else new wsal_parse_in_file($this->fhan);
		
	}
	
	private function set10($a5a) {
		
		$this->low  = $a5a[0];
		$this->high = $a5a[1]; 
		$this->rangen = $a5a[2];
		$fnm = $a5a[3][0];
		$this->fts = $fts  = filemtime($fnm);
		$this->fhan = fopen($fnm, 'r'); unset($fnm);	
		$this->setPtr();
		$this->dhu = date('md-Hi-Y-s', $fts);
		$this->setBuf($a5a);
	}
	
	private function getrow($ar) {
		extract($ar); unset($ar);
		$_id =  sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $i) . '-' . $this->dhu;
		$m = [];
		$m['m10'] = ['fp' => $pp, 'len' => $sll, 'fts' => $this->fts];
		$t = [ '_id' => $_id, 'l' => $l];
		$r = kwam($t, $m);
		return $r;
		
	}
	
	private function setBuf($a5a) {
		$dbn = $a5a[3][1];
		$cnm = $a5a[3][2]; unset($a5a);
		$this->iob = new inonebuf($dbn, $cnm); unset($dbn, $cnm);		
	}
	
	private function setPtr() {
		$p = $this->low;
		$r = $this->fhan;
		if ($p <= 0) return;
		fseek($r, $p - 1); // Always leave it to the previous worker to get a full string
		if (fgetc($r) === "\n") return;
		fgets($r);
	}
	
	private function do40() {

		$p = ftell($this->fhan);
				
		for($i=1; $l = fgets($this->fhan); $i++) {
			$pp = $p;
			$sll = strlen($l);
			$p += $sll;
			$this->dorow(get_defined_vars());
			if ($p > $this->high) break;
		}

		$toti = $this->iob->ino('done - commit');
		
		return $toti; 		
	}
	
	private function dorow($vin) {
		$dat = $this->getRow($vin);
		$this->iob->ino($dat);
	}
	
	
}