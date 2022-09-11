<?php

require_once('/opt/kwynn/kwutils.php');

class remoteBashSession {
	
	const pollius  = 10000;
	const pollis   =	 0;
	const timeoutS =	10;
	const defaultLoginCmd = 'goa'; // goa is my name for ssh [-ipv] ubuntu@kwynn.com -i /path/loginKey.pem "$@"
	const loginInitBytes = 100;
	
	public function __construct($loginCmd = self::defaultLoginCmd) { 
		$this->makeConn($loginCmd);
		$this->login();
	}
	
	private function makeConn($cmd) {
		$pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
		$io;
		$this->procoh = proc_open($cmd, $pdnonce, $io); unset($pdnonce);
		$this->ouh  = $io[0];
		$this->inh  = $io[1]; unset($io);
	}
	
	private function login() {
		$this->getCmdResultI(function($b) { return isset($b[self::loginInitBytes]); });		
	}
	
	public function __destruct() {
		fwrite	  ($this->ouh, 'exit' . "\n");
		fclose	  ($this->inh); 
		fclose	  ($this->ouh);
		proc_close($this->procoh);		
	}

	private function getCmdResultI($cf = null) {

		$h = $this->inh;
		$b = '';
		$ts = 0;
		$enterus = microtime(1);

		do {
			while (is_resource($h) && !feof($h)) {
				$ra = [$h]; $ignorea1 = $ignorea2 = [];
				if (!stream_select($ra, $ignorea1, $ignorea2, self::pollis, self::pollius)) break;
				$c = fgets($h);
				$b  .= $c;
			} 

			if ($cf && $cf($b)) break;

		} while(microtime(1) - $enterus < self::timeoutS);

		echo(	$b);
		return  $b;
	}
	
	public function getCmdRes($cmd, $f) {
		$cmd .= "\n";
		fwrite($this->ouh, $cmd);
		return $this->getCmdResultI($f);
		
	}
	
}

$o = new remoteBashSession();
$o->getCmdRes('stat -c %s /var/log/apache2/access.log', function($b) { return preg_match("/\d+\n/", $b); });
