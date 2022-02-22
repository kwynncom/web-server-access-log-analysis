<?php

require_once('/opt/kwynn/kwutils.php');
// require_once(__DIR__ . '/../' . 'config.php');

class wsal_validator_daemon {
	
	const thef = '/var/kwynn/logs/a10K';
	
	public function  __construct() {
		$this->init10();
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
	
	private function do10() {
		
		$h = fopen(self::thef, 'r');
		$t = fread($h, M_BILLION * 2);
		fclose($h);
				
		fwrite($this->pipw, $t);
		fclose($this->pipw);
		echo(fgets($this->pipr));
	}
}

if (didCLICallMe(__FILE__)) new wsal_validator_daemon();