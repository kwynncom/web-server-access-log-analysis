<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('dao_generic.php');
require_once('loadFile.php');
require_once('loadlive.php');

class bot_cli extends dao_generic_3 {
	
	const dbname = 'wsal';
	
	public static function doit() {
		new self();
	}
	
	private function __construct() {
		$this->db_Init();
		$this->get();
		// $this->do10();
		// $this->getLive();
		$this->do10();
	}
	
	private function db_Init() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines']);
		if (0 && !isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'n' => 1], ['unique' => true]); // lines can be in the same microsecond
	}
	
	private function get() {
		if (0) {$a = loadWSALFile::get();
			if ($a) { $this->rawLinesA = $a; return; } unset($a);
		}
		
		$proj = ['projection' => ['_id' => false, 'n' => true, 'wholeLine' => true]];
		$l1a = $this->lcoll->findOne(['n' => 1], $proj);
		$maxn = $this->getMaxN();
		$lna = $this->lcoll->findOne(['n' => $maxn], $proj);
		
		
		$this->rawLinesA = load_wsal_live::get($l1a, $lna);
		
		return;
		
	}

	private function getMaxN() {
		$group =   [[ '$group' => [ '_id'   => 'aggdat',
						'max'   => ['$max' => '$n'],    ]]		];	
		$res = $this->lcoll->aggregate($group)->toArray();
		return $res[0]['max'];
    }
	
	
	private function do10() {
		$ra = $this->rawLinesA;
		$pa = [];
		$linen = 1;
		foreach($ra as $r) {
			$ta = wsal_parse::parse($r);
			if (!isset($ta['n'])) $ta['n'] = $linen;
			$ta['_id'] = $linen . '-' . str_replace(' ', '', $ta['dateHu']);
			$pa[] = $ta;
			$linen++;
		} $ran = count($ra); $kwc = $ran === count($pa); kwas($kwc, 'bad counts wsal file'); unset($kwc);
		
		if ($pa) $this->db_putAllLines($pa);
		return;
		
	}
	
	public function db_putAllLines($all) { 
		$r = $this->lcoll->insertMany($all);
		kwas($r->getInsertedCount() === count($all), 'bad insert count wsal');
		return;

	}

}

if (didCLICallMe(__FILE__)) bot_cli::doit();