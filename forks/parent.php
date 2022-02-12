<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('worker.php');


class load20_divide extends dao_generic_3 {
	
	const dropUntil = '2022-02-11 23:15';
	// const lfin = '/var/kwynn/mp/m/access.log';
	const lfin = '/var/kwynn/logs/a14M';
	const dbname = 'wsal30';
	const colla   = 'lines';
	
	function __construct() {
		$this->parentLevelDB();
		$this->fsz = $sz = self::getFSZ(self::lfin);
		$this->setFirstTS();
		$bpr = $this->ckdb();
		
		if (!is_numeric($bpr) || $bpr < 0) $bp = 0;
		else $bp = $bpr;
		
		$epr = $sz - 1;
		$bytes = $epr - $bpr + 1;
		echo("parent - attempting file pointer $bpr to $epr / $bytes bytes \n");
		fork::dofork(true, $bpr, $epr, 'wsal_worker', self::lfin, self::dbname, self::colla, $this->fts1);
		
		return;
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}
	
	
	private function setFirstTS() {
		$r = fopen(self::lfin, 'r');
		$l = fgets($r);
		$ts = wsal_parse_2022_010::parse($l, true);
		$this->fts1 = $ts;
		fclose($r);
		
	}
	
	
	private function ckdb() {
		
		return 0;
				
/*		$a = dbqcl::infile(self::dbname, __DIR__ . '/queries.js', 'lastPtr');
		if (!$a) return 0;
		if ($a['fpp1'] > $this->fsz) return -1; // definitely > and not >=
		if ($a['fts' ] > filemtime(self::lfin)) return -1; // same
		$r = fopen(self::lfin, 'r');
		fseek($r, $a['fp0']);
		$iseq = fread($r, $a['llen']) === $a['line'];
		fclose($r);
		if (!$iseq) return -1;
		
		$bpr = $a['fpp1'];
		
		if ($bpr === $this->fsz) {
			echo("file already loaded\n");
			exit(0);
		}
		
		kwas($bpr < $this->fsz, 'this should not happen wsal parent 0132');
		
		return $bpr; */
	}
	
	private function parentLevelDB() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		$this->lcoll->createIndex(['ftsl1' => -1, 'fp0' => -1], ['unique' => true]);
		
		$dd = time() < strtotime(self::dropUntil);
		if (!$dd) return;
		$this->lcoll->drop();		
	}
}

new load20_divide();
