<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao_generic.php');
require_once('loadFile.php');

class bot_cli extends dao_generic_3 {
	
	const dbname = 'wsal';
	
	public static function doit() {
		new self();
	}
	
	private function __construct() {
		$this->db_Init();
		$this->get();
		$this->do10();
	}
	
	private function db_Init() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines']);
		if (0 && !isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'n' => 1], ['unique' => true]); // lines can be in the same microsecond
	}
	
	private function get() {
		$a = loadWSALFile::get();
		$this->rawLinesA = $a;
	}

	private function do10() {
		$ra = $this->rawLinesA;
		$pa = [];
		$linen = 1;
		foreach($ra as $r) {
			$ta = wsal_parse::parse($r);
			$ta['n'] = $linen;
			$ta['_id'] = $linen . '-' . str_replace(' ', '', $ta['dateHu']);
			$pa[] = $ta;
			$linen++;
		} $ran = count($ra); $kwc = $ran === count($pa); kwas($kwc, 'bad counts wsal file'); unset($kwc);
		
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