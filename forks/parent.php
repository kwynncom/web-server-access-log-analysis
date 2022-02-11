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
		$bpr = $this->ckdb();
		
		if (!is_numeric($bpr) || $bpr < 0) $bp = 0;
		else $bp = $bpr;
		
		$epr = $sz - 1;
		$bytes = $epr - $bpr + 1;
		echo("parent - attempting file pointer $bpr to $epr / $bytes bytes \n");
		// fork::dofork(true, $bpr, $epr, ['wsal_worker', 'doit'], self::lfin, self::dbname, self::colla);
		
		// There seems to be a bug when the numbers are so close to 12 CPUs that they aren't divided properly, so call the worker directly.
		wsal_worker::doit($bpr, $epr, 0, [self::lfin, self::dbname, self::colla]);
		
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
		
		$bpr = $a['fpp1'];
		
		if ($bpr === $this->fsz) {
			echo("file already loaded\n");
			exit(0);
		}
		
		kwas($bpr < $this->fsz, 'this should not happen wsal parent 0132');
		
		return $bpr;
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
