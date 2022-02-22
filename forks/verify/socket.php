<?php

require_once('/opt/kwynn/kwutils.php');
require_once('file.php');

class wsal_validate_daemon_socket {

	const port = 61312;
	const maxinput = 60;
	
	public function __construct() {
		$this->fileo = new wsal_validate_daemon_file();
		$this->setListener();
		$this->listen();
	}
	
	public function __destruct() {
		socket_close($this->parsock);
		if (kwifs($this, 'actsock')) {
			socket_close($this->actsock);
		}
	}
	
	private function listen() {
		$this->actsock = $h = socket_accept($this->parsock);
		$ins = socket_read($h, self::maxinput, PHP_NORMAL_READ);
		$outs = $this->doVerify();
		socket_write($h, $outs);
		socket_close($h); $this->actsock = false;	
	}
	
	private function doVerify() {
		return $this->fileo->doit(0, 0);
	}
	
	private function setListener() {
	
		$this->parsock = $sock = socket_create(AF_INET, SOCK_STREAM,  SOL_TCP);
		socket_bind($sock, '127.0.0.1', self::port);
		socket_listen($sock);
	}
}
if (didCLICallMe(__FILE__)) new wsal_validate_daemon_socket();
