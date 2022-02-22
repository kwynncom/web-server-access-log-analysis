<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_validator_daemon {
	
	const thef = '/var/kwynn/logs/a14M';
	const nchunks =   4000;
	const chunks  = 500000;
	const port = 61312;
		
	public function  __construct() {
		$this->openFile();
		$this->openPipes();
		$this->setNsWait();
		// $this->do10();
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
	
	private function setNsWait() {
		$fromn = 0;
		$ton   = filesize(self::thef) - 1;
		$this->do10($fromn, $ton);

	}
	
	private function openFile() {
		$this->fhan = fopen(self::thef, 'r');		
	}
	
	private function do10($fromn, $ton) {
		
		$cki = 0;
		$h = $this->fhan;

		fseek($h, $fromn);
		$reml  = $ton - $fromn + 1;
			
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
			   
		echo(fgets($this->pipr));
		$this->openPipes(); // works, but probably not it's permanent place
	}
	
}

if (didCLICallMe(__FILE__)) new wsal_validator_daemon();