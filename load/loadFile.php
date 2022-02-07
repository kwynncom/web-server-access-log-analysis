<?php

require_once('/opt/kwynn/kwutils.php');

class loadWSALFile {
	const flin = '/tmp/logs/blahblahblah.log';
	const llim = PHP_INT_MAX;
	// const llim = 20;
	
	public static function get() {
		$o = new self();
		return $o->getLines();
	}
	
	public static function getMeta($f = self::flin) {
		return ['n' => self::wc($f), 'head' => self::head($f), 'tail' => self::tail($f)];
	}
	
	public static function wc($f = self::flin) {
		if (!file_exists(self::flin)) return false;	
		$c10 = 'wc -l < ' . self::flin;
		$ln = intval(trim(shell_exec($c10)));
		if (!$ln || $ln < 1) return false;
		return $ln;
	}
	
	public static function head($f = self::flin, $n = 1) { return trim(shell_exec('head -n ' . $n . ' ' . $f . ' 2> /dev/null')); }
	public static function tail($f = self::flin, $n = 1) { return trim(shell_exec('tail -n ' . $n . ' ' . $f . ' 2> /dev/null')); }
	
	private function __construct() {
		$this->theLines = [];
		$this->get10();
	}
	
	private function get10() {
		$ln = self::wc(); 
		if (!$ln) return;
		if ($ln < self::llim) {
			$t = trim(shell_exec('cat -n ' . self::flin));
			$exln = $ln;
		}
		else {
			$c = 'cat -n ' . self::fline . ' tail -n ' . self::llim;
			$t = trim(shell_exec($c)); unset($c);
			$exln = self::llim;
		}
		
		$l = strlen($t); kwas($l > 100, 'log file too small');
		$ra = explode("\n", trim($t)); kwas(count($ra) === $exln, 'not expected lines wsal - 1717'); unset($exln);
		
		$this->theLines = $ra;
		
	}
	
	public function getLines() { return $this->theLines; }

}

if (didCLICallMe(__FILE__)) loadWSALFile::get();
