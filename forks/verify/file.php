<?php

require_once('config.php');

class wsal_validate_daemon_file {
	
	const testf = '/var/kwynn/logs/a50M';
	const livef = '/var/log/apache2/access.log';
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
	
	public static function getFN() {
		if (wsalidl()) return self::livef;
		else		 return self::testf;
	}
	
	private function openFile() {
		$this->fhan = fopen(self::getFN(), 'r');		
	}
	
	public static function areValidFT($from, $to) {
		$t[0] = $from; $t[1] = $to;
		
		try {
			foreach($t as $v) {
				kwas(is_numeric($v), 'invalid 1553kw');
				$v = intval($v);
				kwas($v >= 0 , 'invalid 2239kw', 2239);
			}
			kwas($to < filesize(self::getFN()), 'invalid 2240kw', 2240);
			kwas($from <= $to, 'invalid 2241kw', 2241);
		} catch(Exception $ex) { return FALSE; }
		return TRUE;
	}
	
	public function doit($from, $to) {

		if (!self::areValidFT($from, $to)) return;
		
		$cki = 0;
		$h = $this->fhan;
		
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
