<?php

require_once('config.php');

class wsal_validate_daemon_socket {

	const port = 61312;
	const maxloops = 3000;
	const maxinput = 60;
	const tto = 10;
	
	const hashf = '/var/kwynn/hashes/hwsal10';
	
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
			$t = $this->$s;
			socket_shutdown($t);
			socket_close($t);
		}
	}
	
	public static function ignore_close_warning($errno, $errstr) {
		return;
	}
	
	public static function elow() {
		set_error_handler('kw_error_handler', E_ALL &    (~(E_NOTICE | E_WARNING)));
		set_error_handler(['self', 'ignore_close_warning'], E_NOTICE | E_WARNING);		
	}
	
	public static function ehigh() {
		set_error_handler('kw_error_handler');		
	}
	private function listen() {
		
		$loopi = 0;
		$h = false;
		
		do { ++$loopi;
			/*if ($h && is_resource($h) && get_resource_type($h) !== 'Unknown') {
				self::elow();
				socket_close($h);
				self::ehigh();
			} */
			$ins = false;
			$this->actsock = $h = socket_accept($this->parsock);
			do { ++$loopi;

				if ($h) {
					self::sso($h);
					self::elow();
					$ins = socket_read($h, self::maxinput, PHP_BINARY_READ); // PHP_NORMAL_READ
					self::ehigh();
					if ($ins) {
						$outs = $this->doVV($ins);
						if ($outs) socket_write($h, $outs);
					} 
				}
				if (!$h) break 2; // break 2 seems necessary because it indicates parent is "off"
				if (!$h || !$ins) { 
					if ($h) { 
						socket_shutdown($h);
						socket_close($h); 
						$this->actsock = false;
					}
					break;
				}
				
			} while ($loopi <= self::maxloops);
		} while($loopi <= self::maxloops);
		
		return;
		
	}
	
	private function doVV($s) {
		if (!$s || !isset($s[2])) return '';
		preg_match('/^(\d{1,10}) (\d{1,10})\s+(\w+)\s*(\w*)/', $s, $ms);
		if (!isset($ms[3])) return '';
		wsal_validate_daemon_file::areValidFT($ms[1], $ms[2]);
		$pwt = password_verify($ms[3], $this->pwdh);
		if ($pwt !== true) return '';
		if (kwifs($ms, 4, 0) === 'c') return false;
		$outs = $this->fileo->doit(intval($ms[1]), intval($ms[2]));		
		return $outs;
		
	}

	private static function sso($sock) {
		socket_set_option($sock, SOCK_STREAM, SO_REUSEADDR, 1);		

		if (!wsalidl()) {
			socket_set_option($sock, SOCK_STREAM, SO_RCVTIMEO, ['sec' => self::tto, 'usec' => 0]);		
		}
	}
	
	private function setListener() {
	
		$this->parsock = $sock = socket_create(AF_INET, SOCK_STREAM,  SOL_TCP);
		
		self::sso($sock);
		socket_bind($sock, '0.0.0.0', self::port);
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
