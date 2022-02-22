<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/../' . 'config.php');

class wsal_validator_daemon implements wsal_config {
	
	const thef = '/var/kwynn/logs/a500M';
	
	public function  __construct() {
		$this->init10();
		$this->setNs();
		$this->do10();
	}
	
	public function __destruct() {
		fclose($this->pipr);
		proc_close($this->md4pr);		
	}
	
	private function init10() {
		$pipesInit = [0 => ['pipe', 'r'], 1 => ['pipe', 'w']];
		$this->md4pr = proc_open('openssl md4', $pipesInit, $pipes); unset($pipesInit);
		$this->pipw = $pipes[0];
		$this->pipr = $pipes[1]; unset($pipes);
	
	}
	
	private function setNs() {
		$this->fromn = 0;
		$this->ton   = filesize(self::thef) - 1;
	}
	
	private function do10() {
		
		$cki = 0;
		$h = fopen(self::thef, 'r');
		fseek($h, $this->fromn);
		$ttorl = $this->ton - $this->fromn + 1;
		$progl = 0;
		$reml  = $ttorl;
		
		
		do {
			if ($reml > self::chunks) $itl = self::chunks;
			else					  $itl = $reml;
			
			$s = fread($h, $itl);
			$progl += $itl;
			$reml  -= $itl;
			if (!$s) break;
			fwrite($this->pipw, $s);
			if ($reml <= 0) break;
		} while(++$cki <= self::chunks);
		
		fclose($h);
		fclose($this->pipw);
		echo(fgets($this->pipr));
	}
}

if (didCLICallMe(__FILE__)) new wsal_validator_daemon();