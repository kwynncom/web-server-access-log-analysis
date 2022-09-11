<?php

require_once('remoteBash.php');
require_once(__DIR__ . '/../load/utils/parse.php');

class syncWSAL {
	public function __construct() {
		$this->getLocalH();
		if (0) {
			$this->startRemote();
			$this->getRemoteH();
		}
		$this->createRunningFile();
	}
	
	private function startRemote() {
		$this->rbs = new remoteBashSession();
	}
	
	private function createRunningFile() {
		$p = wsal_parse_2022_010::parse($this->localH);		
		return;
	}
			
	private function getRemoteH() {
		$rh = trim($this->rbs->getCmdRes('head -n 1 /var/log/apache2/access.log', 30));
		kwas($rh === $this->localH, 'head -n 1 does not match wsla files - local and remote');
		return;
		
	}
	
	private function getLocalH() {
		$p = trim(file_get_contents('/var/kwynn/logpath.txt'));
		$cmdf = 'find ' . $p . ' -type f -printf "%T+\t%p\n" | sort -r | grep access | grep log | head -n 1 | awk \'{print $2}\'';
		$l = trim(shell_exec($cmdf));
		$cmdh = "head -n 1 $l";
		$this->localH = $h = trim(shell_exec($cmdh));
		return;
	}
}

// $o = 
// $o->getCmdRes('stat -c %s /var/log/apache2/access.log', function($b) { return preg_match("/\d+\n/", $b); });

// 

new syncWSAL();

