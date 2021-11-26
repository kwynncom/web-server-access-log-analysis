<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao_generic.php');

class bot_cli extends dao_generic_3 {
	
	const flin = '/tmp/logs/access.log';
	// const llim = PHP_INT_MAX;
	const llim = 20;
	
	const dbname = 'wsal';
	
	public static function doit() {
		new self();
	}
	
	private function __construct() {
		$this->db_Init();
		$this->pdat10();
		$this->do10();
		if(0) {$this->do20();
		$this->do30();}
	}
	
	private function pdat10() {
		$md5a = $this->lcoll->distinct('fmd5');
		$n   = $this->lcoll->count();
		
		return;
	}
	
	private function db_Init() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines', 'm' => 'meta']);
		if (0 && !isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'linen' => 1], ['unique' => true]); // 408 lines can be in the same microsecond
		$this->lcoll->createIndex(['fmd5' => -1, 'linen' => 1], ['unique' => true]);
	}
	
	private function do30() {
		var_dump($this->aga);
	}
	
	private function get() {
		$c = 'wc -l < ' . self::flin;
		$ln = intval(shell_exec($c)); kwas($ln >= 1, 'no lines in log file');
		$this->totLinesWC = $ln;
		$this->fmd5 = trim(shell_exec('md5sum ' . self::flin . " | cut -d ' ' -f 1"));
		if ($ln < self::llim) return file_get_contents(self::flin);
		$c = 'tail -n ' . self::llim . ' ' . self::flin;
		return shell_exec($c);
		
	}
	
	private function do20() {
		$ra = $this->para;
		foreach($ra as $r) {
			if ($r['iserr']) continue;
			$agr = $r['agent'];
			$ag = $agr;
			if (!isset($a[$ag])) $a[$ag] = 0;
			$a[$ag]++;
		}
		
		asort($a);
		$this->aga = $a;
		return;
	}
	
	private function do10() {
		$t = $this->get();
		return; // *****
		$l = strlen($t); kwas($l > 100, 'log file too small');
		$ra = explode("\n", trim($t));
		$pa = [];
		$linen = 1;
		$this->hterrs = 0;
		foreach($ra as $r) {
			$ta = wsal_parse::parse($r);
			$ta['linen'] = $linen;
			$ta['fmd5'] = $this->fmd5;
			$ta['_id'] = $linen . '-' . str_replace(' ', '', $ta['dateHu']);
			$ise = $ta['iserr'] = $ta['httpCode'] >= 400 ? true : false;
			if ($ise) $this->hterrs++;
			$pa[] = $ta;
			$linen++;
		} $ran = count($ra);	
		$kwc = $ran === count($pa) && ($ran === self::llim || $ran === $this->totLinesWC);
		kwas($kwc, 'bad counts wsal file'); unset($kwc);
		
		$this->db_putAllLines($pa);
		
		$this->para = $pa;
		return;
		
	}
	
	public function db_putAllLines($all) { 
		$r = $this->lcoll->insertMany($all);
		kwas($r->getInsertedCount() === count($all), 'bad insert count wsal');
		return;

	}

}

if (didCLICallMe(__FILE__)) bot_cli::doit();