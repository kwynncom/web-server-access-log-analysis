<?php

require_once('/opt/kwynn/kwutils.php');
require_once('/opt/kwynn/mongodb2.php');

class load_wsal_live extends dao_generic_2 {

    const dbName = 'wsalogs';
    const basecmd = '/var/kwynn/goa ';
    const ttpf = '/tmp/tl.log';
    
    public function __construct() {
	parent::__construct(self::dbName, __FILE__);
	$this->creTabs(['l' => 'lines']);
	$thei = $this->lcoll->createIndex(['md5' => 1, 'i' => 1], ['unique' => true]);
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
	
	$fp = self::ttfp;
	$fp1r = file_put_contents($fp, '');
	kwas(chmod($fp, 0600), 'chmod fail');
	file_put_contents($fp, $l);
	echo($l);
	return $l;
	
    }
    
    private function l05() {
	if (file_exists(self::ttpf)) $lss = file_get_contents(self::ttpf);
	else $lss = $this->l10();
	$this->match($lss);
    }
    
    
    private function match($lss) { 
	$dat = [];
	$lsa = explode("\n", $lss);
	$len = count($lsa);
	
	for ($i=0; $i + 1 < $len; $i++) {
	    $md50 = md5($lsa[$i]);
	    // $this->
	    
	}
	
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