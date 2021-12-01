<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao_generic.php');

class bot_cli extends dao_generic_3 {
	
	const flin = '/tmp/logs/access.log';
	const llim = PHP_INT_MAX;
	// const llim = 20;
	
	const dbname = 'wsal';
	
	public static function doit() {
		new self();
	}
	
	private function __construct() {
		$this->db_Init();
		$this->do10();
	}
	

	
	private function db_Init() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines']);
		if (0 && !isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'n' => 1], ['unique' => true]); // lines can be in the same microsecond
	}
	
	private function get() {
		$c = 'wc -l < ' . self::flin;
		$ln = intval(shell_exec($c)); kwas($ln >= 1, 'no lines in log file');
		$this->totLinesWC = $ln;
		if ($ln < self::llim) return file_get_contents(self::flin);
		$c = 'tail -n ' . self::llim . ' ' . self::flin;
		return shell_exec($c);
		
	}

	private function do10() {
		$t = $this->get();

		if (is_array($t)) return;
		
		$l = strlen($t); 
		
		kwas($l > 100, 'log file too small');
		$ra = explode("\n", trim($t));
		$pa = [];
		$linen = 1;
		foreach($ra as $r) {
			$ta = wsal_parse::parse($r);
			$ta['n'] = $linen;
			$ta['_id'] = $linen . '-' . str_replace(' ', '', $ta['dateHu']);
			$pa[] = $ta;
			$linen++;
		} $ran = count($ra);	
		$kwc = $ran === count($pa) && ($ran === self::llim || $ran === $this->totLinesWC);
		kwas($kwc, 'bad counts wsal file'); unset($kwc);
		
		$this->db_putAllLines($pa);
		return;
		
	}
	
	public function db_putAllLines($all) { 
		$r = $this->lcoll->insertMany($all);
		kwas($r->getInsertedCount() === count($all), 'bad insert count wsal');
		return;

	}

}

if (didCLICallMe(__FILE__)) bot_cli::doit();