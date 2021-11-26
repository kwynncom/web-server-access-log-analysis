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
		$this->creMeta10();
		$this->do10();
		if(1) {$this->do20();
		$this->do30();}
	}
	
	private function creMeta10() {
		$md5a = $this->lcoll->distinct('fmd5');
		
		$proj = ['projection' => ['_id' => 0, 'dateHu' => 1, 'tsus' => 1]];
		
		foreach($md5a as $i => $md5) {
			$n   = $this->lcoll->count(['fmd5' => $md5]); kwas($n >= 1, 'bad count');
			$fa = $d['1_'] = $this->lcoll->findOne(['linen' => 1]  , $proj); kwas(count($fa) >= 2, 'bad line 1 wsal');
			$la = $d['n_'] = $this->lcoll->findOne(['linen' =>  $n], $proj); kwas(count($la) >= 2, 'bad line n wsal');
		
			foreach($d as $pk => $pa) 
				foreach($pa as $k => $v) $d5[$pk . $k] = $v;
		
			$d5['1ln_md5'] = $md5;

			$id = $fa['dateHu'] . '-' . $la['dateHu'] . '-n-' . $n . '-' . $md5;
			$id = str_replace(' ', '' , $id);
			
			$d5['_id'] = $id;
			$d5['n']   = $n;
			
			$this->mcoll->upsert(['1ln_md5' => $md5], $d5);
			
			continue;
		}
		

		
		// $this->

		return;
	}
	
	private function ckmeta($md5, $n) {
		if ($this->mcoll->count(['1ln_md5' => $md5, 'n' => $n]) === 1) return true;
		return false;
	}
	
	private function db_Init() {
		parent::__construct(self::dbname);
		$this->creTabs(['l' => 'lines', 'm' => 'meta']);
		if (0 && !isAWS()) $this->lcoll->drop();
		$this->lcoll->createIndex(['tsus' => -1, 'linen' => 1], ['unique' => true]); // 408 lines can be in the same microsecond
		$this->lcoll->createIndex(['fmd5' => -1, 'linen' => 1], ['unique' => true]);
		$this->lcoll->createIndex(['fmd5' => -1]								  );
		$this->mcoll->createIndex(['1ln_md5'  => -1]		  , ['unique' => true]);
	}
	
	private function do30() {
		var_dump($this->aga);
	}
	
	private function get() {
		$c = 'wc -l < ' . self::flin;
		$ln = intval(shell_exec($c)); kwas($ln >= 1, 'no lines in log file');
		$this->totLinesWC = $ln;
		$hln = shell_exec('head -n 1 ' . self::flin);
		$md5 = $this->fmd5 = md5(trim($hln));
		if ($this->ckmeta($md5, $ln)) return [$md5, $ln];
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

		if (is_array($t)) {
			$proj = ['projection' => ['_id' => 0, 'agent' => 1, 'iserr' => 1]];
			$ta = $this->lcoll->find(['fmd5' => $t[0]], $proj); kwas(count($ta) === $t[1], 'bad db count wsal - 0238');
			$this->para = $ta;
			return;

		}
		
		$l = strlen($t); 
		
		kwas($l > 100, 'log file too small');
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