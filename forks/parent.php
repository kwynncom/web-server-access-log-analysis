<?php

require_once('/opt/kwynn/kwutils.php');
require_once('worker.php');

class load20_divide extends dao_generic_3 {
	
	const dropUntil = '2022-02-11 00:30';
	const lfin = '/var/kwynn/mp/m/access.log';
	const dbname = 'wsal';
	const colla   = 'lines';
	
	function __construct() {
		$this->parentLevelDB();
		$this->fsz = $sz = self::getFSZ(self::lfin);
		$dbr = $this->ckdb();
		
		if (!is_numeric($dbr) || $dbr < 0) $bp = 0;
		else $bp = $dbr;
		
		$epr = $sz - 1;
		$bytes = $epr - $dbr + 1;
		echo("parent - attempting file pointer $dbr to $epr / $bytes bytes \n");
		fork::dofork(true, $dbr, $epr, ['wsal_worker', 'doit'], self::lfin, self::dbname, self::colla);
		return;
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}
	
	private function ckdb() {
				
		$a = dbqcl::infile(self::dbname, __DIR__ . '/queries.js', 'lastPtr');
		if (!$a) return -1;
		if ($a['fpp1'] > $this->fsz) return -1; // definitely > and not >=
		if ($a['fts' ] > filemtime(self::lfin)) return -1; // same
		$r = fopen(self::lfin, 'r');
		fseek($r, $a['fp0']);
		$iseq = fread($r, $a['llen']) === $a['line'];
		fclose($r);
		if (!$iseq) return -1;
		
		$dbr = $a['fpp1'];
		
		if ($dbr === $this->fsz) {
			echo("file already loaded\n");
			exit(0);
		}
		
		kwas($dbr < $this->fsz, 'this should not happen wsal parent 0132');
		
		return $dbr;
	}
	
	private function parentLevelDB() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		
		$dd = time() < strtotime(self::dropUntil);
		if (!$dd) return;
		$this->lcoll->drop();		
	}
}

new load20_divide();
