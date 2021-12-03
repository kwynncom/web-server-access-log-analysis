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
		$this->linesAdded = 0;
		$this->db_Init();
		$this->getFile();
		$this->getLive();
		echo($this->linesAdded . ' lines added' . "\n");
	}
	
	private function db_Init() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines']);
		if (0 && !isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'n' => -1], ['unique' => true]); // lines can be in the same microsecond
	}
	
	private function alreadyFile() {
		try {
			$fa = loadWSALFile::getMeta(); kwas($fa && is_array($fa));
			$da = $this->getDB1n($fa['n']);		   kwas($da && is_array($da));
			$ln = $da['l1a']['wholeLine'];
			wsal_parse::setNDat($ln, $n);
			kwas($fa['head'] === $ln && $n['n'] === 1); unset($ln, $n);
			$ln = $da['lna']['wholeLine'];
			wsal_parse::setNDat($ln, $n);
			kwas($fa['tail'] === $ln && $n['n'] === $fa['n']); unset($ln, $n);			
		} catch(Exception $ex) { 
			return FALSE; 
		}
		return TRUE; // *****
	}
	
	private function getFile() {
		if ($this->alreadyFile()) return;
		$a = loadWSALFile::get();
		$this->p10($a);
	}
	
	private function getDB1n($nin = false) {
		$proj = ['projection' => ['_id' => false, 'n' => true, 'wholeLine' => true]];
		$l1a = $this->lcoll->findOne(['n' => 1], $proj);
		if (!$nin) $n = $this->getMaxN();
		else	   $n = $nin; unset($nin);
		$lna = $this->lcoll->findOne(['n' => $n], $proj); unset($proj);
		return get_defined_vars();
	}

	private function dbintegrityCk10() {
		$dbr = $this->getDB1n();
		kwas($this->lcoll->count() === $dbr['n'], 'db count not equal to maxn');
		kwas($dbr['lna']['n'] === $dbr['n'], 'db dat int ck 10 fail lann');
	}
	
	private function getLive() {
		$dbr = $this->getDB1n();
		extract($dbr); unset($dbr);
		$new = load_wsal_live::get($l1a, $lna);
		$this->p10($new);
		
		return;
		
	}

	private function getMaxN() {
		$group =   [[ '$group' => [ '_id'   => 'aggdat',
						'max'   => ['$max' => '$n'],    ]]		];	
		$res = $this->lcoll->aggregate($group)->toArray();
		return $res[0]['max'];
    }
	

	
	
	private function p10($ra) {
		if (!$ra) return;
		$pa = [];
		foreach($ra as $r) {
			$ta = wsal_parse::parse($r);
			$ta['_id'] = $ta['n'] . '-' . str_replace(' ', '', $ta['dateHu']);
			$pa[] = $ta;
		} $ran = count($ra); $kwc = $ran === count($pa); kwas($kwc, 'bad counts wsal file'); unset($kwc);
		
		if ($pa) $this->db_putAllLines($pa);
		return;
		
	}
	
	public function db_putAllLines($all) { 
		$cnt = count($all);
		$r = $this->lcoll->insertMany($all);
		kwas($r->getInsertedCount() === $cnt, 'bad insert count wsal');
		$this->linesAdded += $cnt;
		$this->dbintegrityCk10();
		return;

	}

}

if (didCLICallMe(__FILE__)) bot_cli::doit();