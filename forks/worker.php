<?php

require_once('parse.php');
require_once(__DIR__ . '/../load/parse.php');

// temporary db ********************************
class log_load_worker extends dao_generic_3 {
	public static function doit(...$args) {
		new self($args);
	}

	private function __construct($a5a) {
		
		parent::__construct('wsal20');
		$this->creTabs('lines');
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
			$pp = $p;
			$llen = strlen($l);
			$p += $llen;
			$_id = sprintf('%02d', $this->rangen) . '-' . sprintf('%07d', $i) . '-' . $this->dhu;
			$dv = get_defined_vars();
			$pa = wsal_parse_in_file::parse($l);
			$dat = kwam($dv, $pa);
			try { 
				$this->lcoll->insertOne($dat);
			} catch(Exception $ex) {
				kwynn();
				exit(0);
			} unset($dv, $dat); // *** otherwise recursion!!!
			// exit(0); // ********
			// $this->iob->ino($dat);
			if ($p > $this->high) break;
		}

		$toti = $this->iob->ino('done - commit');
		
		return $toti; 		
	}
}