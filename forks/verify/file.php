<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_validator_daemon {
	
	const thef = '/var/kwynn/logs/a400M';
	const nchunks =   4000;
	const chunks  = 500000;
	const port = 61312;
		
	public function  __construct() {
		$this->init10();
		$this->setNs();
		// $this->do10();
	}

	private function init10() {
		$pipesInit = [0 => ['pipe', 'r'], 1 => ['pipe', 'w']];
		$this->md4pr = proc_open('openssl md4', $pipesInit, $pipes); unset($pipesInit);
		$this->pipw = $pipes[0];
		$this->pipr = $pipes[1]; unset($pipes);
	
	}
	
	private function setNs() {
		// $this->fromn = 0;
		// $this->ton   = filesize(self::thef) - 1;
		/*
		$sock = socket_create(AF_INET, SOCK_STREAM,  SOL_TCP);
		socket_bind($sock, '127.0.0.1', self::port);
		socket_listen($sock);
		$h = socket_accept($sock);
		socket_read($h, 100, PHP_NORMAL_READ); */
	}
	
	private function do10() {
		
		$cki = 0;
		$h = fopen(self::thef, 'r');
		fseek($h, $this->fromn);
		$reml  = $this->ton - $this->fromn + 1;
			
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
		echo(fgets($this->pipr));
		fclose($h);
		fclose($this->pipr);
		proc_close($this->md4pr);
	}
}

if (didCLICallMe(__FILE__)) new wsal_validator_daemon();