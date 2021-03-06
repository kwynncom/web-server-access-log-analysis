<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/mongodb2.php');
require_once('dao.php');
require_once('doit.php');

class load_wsal_live extends dao_wsal {

    const basecmd = '/var/kwynn/goa ';
    const ttpf = '/tmp/tl.log';
    const testMode = 0;
    
    public function __construct() {
	parent::__construct();
	// die('turning off for now');
	$this->l05();
	return;
    }
    
    private function wc() { return intval(trim(shell_exec(self::basecmd . "'" . 'wc -l < /var/log/apache2/access.log' . "'")));  }
    
    private function l10() {
	$maxdb = $this->maxdb();
	$maxlv = $this->wc();
	$n10 = $maxlv - $maxdb;
	$n = $n10 + 20;
	$l = shell_exec(self::basecmd . "'" . "tail -n $n /var/log/apache2/access.log" . "'");
	
	$fp = self::ttpf;
	$fp1r = file_put_contents($fp, '');
	kwas(chmod($fp, 0600), 'chmod fail');
	file_put_contents($fp, $l);
	return $l;
	
    }
    
    private function l05() {
	if (file_exists(self::ttpf && self::testMode)) $lss = file_get_contents(self::ttpf);
	else $lss = $this->l10();
	$this->match($lss);
    }
    
    
    private function match($lss) { 
	$t = [];
	$lsa = explode("\n", $lss);
	$len = count($lsa);
	$dat = [];
	
	$dbr = false;
	for ($i=0; $i  < $len; $i++) {
	    
	    if ($i === 19) {
		$ignore = 2;
		
	    }
	    
	    $l = $lsa[$i];
	    
	    if (!trim($l)) continue; // should be written into dao, too
	    
	    $md5 = md5($l);
	    $t[$i]['md5'] = $md5;
	    $t[$i]['line'] = $l;
	    if (!isset($ioff) || isset($dbr)) 
		$dbr = $this->lcoll->findOne(['md5' => $md5], ['sort' => ['i' => 1]]);
	    if (!isset($ioff)) {

		if ($dbr) $t[$i]['i'] = $dbr['i'];
		if (!isset($lsa[$i-1])) continue;
		if (	$t[$i]['md5'] !== $t[$i-1]['md5']
			&&  $t[$i]['i']   === $t[$i-1]['i'] + 1
			) $ioff = $t[$i]['i'] - $i;
	    }
	    
	    if (!isset($ioff)) continue; // if no new lines exist, this happens
	    
	    $t[$i]['i'] = $i + $ioff;
	    if (isset($dbr)) continue;
	    
	    $t[$i] = wsal_21_1::addAnal($t[$i]);
	    
	    $dat[] = $t[$i]; unset($t[$i]);
	}
	
	if ($dat) {
	    $this->lcoll->insertMany($dat);
	    echo(count($dat) . ' lines loaded' . "\n");
	} else echo("no new lines\n");
	
	
	return;
	
    }
    
    private function maxdb() {
	
	$group =   [[ '$group' => [
				    '_id'   => 'aggdat',
				    'max'   => ['$max' => '$i'],
		   ]]		];	
	
	$res = $this->lcoll->aggregate($group)->toArray();
	return $res[0]['max'];
	
	
    }
    
    
}

if (didCLICallMe(__FILE__)) new load_wsal_live();