<?php

require_once('/opt/kwynn/kwutils.php');
require_once('parse.php');
require_once('worker.php');
require_once('verify.php');


class load20_divide extends dao_generic_3 {
	
	const dropUntil = '2022-02-11 20:59';
	const lfin = '/var/kwynn/mp/m/access.log';
	// const lfin = '/var/kwynn/logs/a500M';
	const dbname = 'wsal';
	const colla   = 'lines';
	
	function __construct() {
		$this->parentLevelDB();
		$this->fsz = $sz = self::getFSZ(self::lfin);
		$bpr = $this->ckdb();
		$epr = -1;
		$creat = time();
		if (is_numeric($bpr)) {
			$epr = $sz - 1;
			$bytes = $epr - $bpr + 1;
			echo("parent - attempting file pointer $bpr to $epr / $bytes bytes \n");
			fork::dofork(true, $bpr, $epr, 'wsal_worker', self::lfin, self::dbname, self::colla, $this->fts1, $creat);
		}
		
		if (time() < strtotime('2022-02-12 19:30'))
			new wsal_verify(self::dbname, self::colla, self::lfin, $this->lcoll->count(['ftsl1' => $this->fts1]), $this->fts1, $this->fsz, 
								$bpr, $epr);
		
		return;
	}
	
	private static function getFSz($f) {
		kwas(is_readable($f), 'file not readable');
		$sz =   filesize($f);	
		return $sz;
	}

	private function ckdb() {
		
		$h = $this->fhan = $h = fopen(self::lfin, 'r');
		$l = fgets($h);
		$ts = wsal_parse_2022_010::parse($l, true);
		$this->fts1 = $ts;
		$sz = $this->fsz;

		$q = "db.getCollection('lines').find({'ftsl1' : $this->fts1 }).sort({'fpp1' : -1}).limit(1)";
		$a = dbqcl::q(self::dbname, $q);
		fclose($h);
		
		if (!$a) return 0;
		if ($a['fpp1'] === $sz) {
			echo("file already loaded\n");
			return false;
		}
		
		return $a['fpp1'];
	}
	
	private function parentLevelDB() {
		parent::__construct(self::dbname);
		$this->creTabs(self::colla);
		// $this->lcoll->createIndex(['ftsl1' => -1, 'fp0' => -1], ['unique' => true]);
		
		$dd = time() < strtotime(self::dropUntil);
		if (!$dd) return;
		$this->lcoll->drop();		
	}
}

new load20_divide();
