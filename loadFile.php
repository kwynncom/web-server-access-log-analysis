<?php

require_once('/opt/kwynn/kwutils.php');

class loadWSALFile {
	const flin = '/tmp/logs/access.log';
	const llim = PHP_INT_MAX;
	// const llim = 20;
	
	public static function get() {
		$o = new self();
		return $o->getLines();
	}
	
	private function __construct() {
		$this->get10();
	}
	
	private function get10() {
		$c10 = 'wc -l < ' . self::flin;
		$ln = intval(shell_exec($c10)); kwas($ln >= 1, 'no lines in log file'); unset($c10);
		$this->totalLinesWC = $ln;
		if ($ln < self::llim) {
			$t = file_get_contents(self::flin);
			$exln = $ln;
		}
		else {
			$c = 'tail -n ' . self::llim . ' ' . self::flin;
			$t = shell_exec($c); unset($c);
			$exln = self::llim;
		}
		
		$l = strlen($t); kwas($l > 100, 'log file too small');
		$ra = explode("\n", trim($t)); kwas(count($ra) === $exln, 'not expected lines wsal - 1717'); unset($exln);
		
		$this->theLines = $ra;
		
	}
	
	public function getLines() { return $this->theLines; }

}

if (didCLICallMe(__FILE__)) loadWSALFile::get();
