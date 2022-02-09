<?php

require_once('parse.php');
require_once(__DIR__ . '/../load/parse.php');

class log_load_worker {
	public static function doit(...$args) {
		new self($args);
	}

	private function __construct($a5a) {
		$this->set10($a5a);
		$this->do40 ($a5a);
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
	
	private function getrowMD($i) {
		return sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $i) . '-' . $this->dhu;
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
			$fp0 = $p;
			$llen = strlen($l);
			$p += $llen;
			$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $i) . '-' . $this->dhu;
			$fpep1 = $p;
			$fts = $this->fts;
			$dv = get_defined_vars(); unset($dv['p'], $dv['i']);
			$this->iob->ino($dv); unset($dv); // otherwise infinite recursion!!!!!
			if ($p > $this->high) break;
		}

		$toti = $this->iob->ino('done - commit');
		
		return $toti; 		
	}
}