<?php

require_once('/opt/kwynn/kwutils.php');
require_once('file.php');

class wsal_validate_daemon_socket {

	const port = 61312;
	
	const maxinput = 60;
	const hashf = '/var/kwynn/hashes/wsal_v221/hash';
	
	public function __construct() {
		$this->setPwdHash();
		if (1) {
			$this->fileo = new wsal_validate_daemon_file();
			$this->setListener();
			$this->listen(); 
		}
	}
	
	public function __destruct() {
		$ss = ['parsock', 'actsock'];
		foreach($ss as $s) if (kwifs($this, $s)) {
			socket_close($this->$s);
		}
	}
	
	public static function ignore_close_warning($errno, $errstr) {
		return;
	}
	
	private function listen() {
		
		do {
			$this->actsock = $h = socket_accept($this->parsock);
			do {
				set_error_handler('kw_error_handler', E_ALL &    (~(E_NOTICE | E_WARNING)));
				set_error_handler(['self', 'ignore_close_warning'], E_NOTICE | E_WARNING);
				if (!$h) break 2; // such as timeout of parent
				$ins = socket_read($h, self::maxinput, PHP_NORMAL_READ);
				set_error_handler('kw_error_handler');
				if ($ins === false) { socket_close($h); $this->actsock = false;	break; }
				$outs = $this->doVV($ins);
				if ($outs) socket_write($h, $outs);
				if ($outs === false) break 2;
			} while (1);
		} while(1);
		
	}
	
	private function doVV($s) {
		if (!$s || !isset($s[2])) return '';
		preg_match('/^(\d{1,10}) (\d{1,10})\s+(\w+)\s*(\w*)/', $s, $ms);
		if (!isset($ms[3])) return '';
		$pwt = password_verify($ms[3], $this->pwdh);
		if ($pwt !== true) return '';
		if (kwifs($ms, 4, 0) === 'c') return false;
		$outs = $this->fileo->doit(intval($ms[1]), intval($ms[2]));		
		return $outs;
		
	}

	private function setListener() {
	
		$this->parsock = $sock = socket_create(AF_INET, SOCK_STREAM,  SOL_TCP);
		
		if (get_current_user() !== 'ubuntu')
			socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 60, 'usec' => 0]);
		socket_bind($sock, '127.0.0.1', self::port);
		socket_listen($sock);

	}
	
	private function setPwdHash() {
		$s = trim(file_get_contents(self::hashf));		
		$a = password_get_info($s);
		kwas(is_array($a) && count($a) >= 3 && is_array($a['options']) && count($a['options']) >= 3, 'bad stored file wsal kw0109');
		$this->pwdh = $s;
		return;
	}
}

if (didCLICallMe(__FILE__)) new wsal_validate_daemon_socket();
