<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_validate_daemon_file {
	
	const thef = '/var/kwynn/logs/a50M';
	const nchunks =   4000;
	const chunks  = 500000;

	public function  __construct($callme = false) {
		$this->openFile();
		$this->openPipes();
		if ($callme) echo($this->doit(0, 30 * M_MILLION - 1));
	}

	public function __destruct() {
		fclose($this->fhan);
		$this->closePipes();

	}
	
	private function openPipes() {
		$pipesInit = [0 => ['pipe', 'r'], 1 => ['pipe', 'w']];
		$this->md4pr = proc_open('openssl md4', $pipesInit, $pipes); unset($pipesInit);
		$this->pipw = $pipes[0];
		$this->pipr = $pipes[1]; unset($pipes);
	
	}
	
	private function closePipes() {
		fclose($this->pipr);
		if ($this->pipw) fclose($this->pipw);
		$this->pipw = false;
		proc_close($this->md4pr);	
	}
	
	private function openFile() {
		$this->fhan = fopen(self::thef, 'r');		
	}
	
	private function areValidFT(int $from, int $to) {
		$t[0] = $from; $t[1] = $to;
		foreach($t as $v) kwas($v >= 0 , 'invalid 2239kw', 2239);
		kwas($to < filesize(self::thef), 'invalid 2240kw', 2240);
		kwas($from <= $to, 'invalid 2241kw', 2241);
		
	}
	
	public function doit($from, $to) {
		
		$cki = 0;
		$h = $this->fhan;

		$this->areValidFT($from, $to);
		
		fseek($h, $from);
		$reml  = $to - $from + 1;
			
		do {
			if ($reml > self::chunks) $itl = self::chunks;
			else					  $itl = $reml;
			
			$s = fread($h, $itl);
			$reml  -= $itl;
			if (!$s) break;
			fwrite($this->pipw, $s);
			if ($reml <= 0) break;
		} while(++$cki <= self::nchunks);

		fclose($this->pipw); 
			   $this->pipw = false;
			   
		$res = fgets($this->pipr);
		$this->closePipes();
		$this->openPipes();
		
		return $res;
	}
	
}

if (didCLICallMe(__FILE__)) new wsal_validate_daemon_file(true);
