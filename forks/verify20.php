<?php

require_once('config.php');
require_once('forker.php');

class wsal_verify_20 extends dao_generic_3 implements wsal_config {
	
	const vcoll = 'verify';
	// const tfpp1 = 999883;	
	// const tfpp1 = 194;
	const tfpp1 = false;
	
	// 				new $thecl(true, $i, $fargs);
	public function __construct($isw = false, $wn = -1, $exas = false) {
		
		if ($exas) $exas = $exas[0];
		
		parent::__construct(self::dbname);
		$this->creTabs(self::vcoll);

		if (!$isw) {
			$vs = $this->init10();
			$this->fork($vs);
			unset($vs);
		} 
		else if ($wn === 1) $this->dbv($exas);		  
		else if ($wn === 2) $this->fv ($exas);
		
		
	}
	
	private function fork($vs) {
		fork_roundRobin::dofork(false, 1, 2, 'wsal_verify_20', $vs);
	}
	
	private function fv($vsin) {
		extract($vsin); unset($vsin);		
		$c = '';
		$c .= 'goa "';
		$c .= "head -c $fpp1 ";
		$c .= ' /var/log/apache2/access.log ';
		if ($fp0) {
			$d = $fpp1 - $fp0;
			$c .= " | tail -c $d "; unset($d);
		}
		$c .= ' | openssl md4';
		$c .= '"';
		
		echo("$c\n");
		$md4_v_f = self::extractMD(shell_exec($c)); unset($c);
	
		$dat = get_defined_vars();

		$this->upsert($dat);
				
		print_r($dat);
	}
	
	private function upsert($dat) {
		$q = ['_id' => $dat['_id']];
		$this->vcoll->upsert($q, $dat, 1, false);		
	}
	
	private function dbv($vsin) {
		extract($vsin); unset($vsin);
		$q = "db.getCollection('lines').find({ ftsl1 : $ftsl1, fp0: { \$gte: $fp0 }, fpp1 : { \$lte : $fpp1  }})";
		echo($q . "\n");
		$q .= ".sort({ fpp1 : 1})";
		$q .= '.forEach(function(r) { print(r.line.trim()); })';

		$md4_v_db = self::extractMD(dbqcl::q(self::dbname, $q, false, false, true, ' | openssl md4', true)); unset($q);

		$dat = get_defined_vars();

		$this->upsert($dat);				
		print_r($dat);
	
	}
	
	private function init10() {
		$ftsl1 = wsal_load_forks::getL1AndCk(self::lfin);
		
		if (!self::tfpp1) {
			$q = "db.getCollection('lines').find({ftsl1 : $ftsl1},  {fpp1 : 1, _id : 0}).sort({fpp1 : -1}).limit(1)";
			$fpp1 = dbqcl::q(self::dbname, $q); 
			unset($q);
		} else $fpp1 = self::tfpp1;

$q = <<<QLV
		db.getCollection('verify').find({ftsl1 : $ftsl1, md4_v_db : { \$exists : true}, 
    \$expr :  { \$and : [{\$eq : ['\$md4_v_db', '\$md4_v_f']}, {\$eq :[{\$strLenBytes : '\$md4_v_db'}, 32]}] }}, 
		{fpp1 : true, _id : false})
QLV;
$q .= ".sort({ fpp1 : -1}).limit(1)";
		
		$fp0 = 	dbqcl::q(self::dbname, $q); unset($q);
		
		if ($fp0 === $fpp1) {
			echo("already verified\n");
			exit(0);
		} kwas($fp0 < $fpp1, 'this inequality should probably not happen wsal verify file pointers kw');
		

		$_id = date('m-d-H:i:s-Y', $ftsl1) . '_' . $fp0 . '_' . $fpp1;
	
		$this->upsert(['_id' => $_id]); // prevent the potential forking race condition
		
		return get_defined_vars();
	}
	
	public static function extractMD($s) {
		kwas(preg_match('/\s([0-9a-f]{32})/', $s, $ms), 'no md4 found wsal verify');
		return trim($ms[1]);
	}
	
	public static function shouldSplit (int $low, int $high, int $cpuCount) { return true; }

	
}

class fork_roundRobin {
	public static function dofork($reallyFork, $startat, $endat, $thecl, ...$fargs) {

		$cpids = [];
		for ($i=$startat; $i <= $endat; $i++) {
		    $pid = -1;	
			if ($reallyFork) $pid = pcntl_fork();
			if ($pid === 0 || !$reallyFork) {
				new $thecl(true, $i, $fargs);
				if ($reallyFork) exit(0);
			}  
			$cpids[$i] = $pid;	
		}

		if ($reallyFork) for($i=$startat; $i <= $endat; $i++) pcntl_waitpid($cpids[$i], $status);
	}
}

if (didCLICallMe(__FILE__)) new wsal_verify_20();