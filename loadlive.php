<?php

require_once('/opt/kwynn/kwutils.php');
// require_once('loadDB.php');

class load_wsal_live /* extends dao_wsal*/ {

    const basecmd = '/var/kwynn/goa ';
    const pidf    = '/tmp/lld_load_live_d_wsal_kwynn_com_ns2021_03_1.pid';
    
    public function __construct() {
	// parent::__construct();
	self::setRun();
	$this->setDMode();
	$this->l05();
	if ($this->dmode) $this->lc20(); // load continuous / -f option
	return;
    }
    
    public function __destruct() {
	if (!fclose($this->inh)) echo('fclose() failed');
	$pcr = proc_close($this->proh);
	kwas($pcr === 0, 'proc_close failed');
    }
    
    private function wc() { return intval(trim(shell_exec(self::basecmd . "'" . 'wc -l < /var/log/apache2/access.log' . "'")));  }
    
    private function l10() {
	$maxdb = $this->maxdb();
	$maxlv = $this->wc();
	$n10 = $maxlv - $maxdb;
	$n = $n10 + 20;
	
	$cmd = self::basecmd . "'" . "tail -n $n ";
	if ($this->dmode) $cmd .= ' -f ';
	$cmd .= '/var/log/apache2/access.log' . "'";
	
	$this->proh = proc_open($cmd, [1 => ['pipe', 'w']], $pipes);
	$this->inh  = $pipes[1]; unset($pipes);
	
	$lns = '';
	for($i=0; $i < $n; $i++) $lns .= fgets($this->inh);
		
	return $lns;
    }
    
    private function l05() {
	$lss = $this->l10();
	$this->match($lss);
    }
     
    private function match($lss) { 
	$t = [];
	$lsa = explode("\n", $lss);
	$len = count($lsa);
	$dat = [];
	
	$dbr = false;
	for ($i=0; $i  < $len; $i++) {
	    
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
	    
	    $curri = $i + $ioff;
	    $t[$i]['i'] = $curri;
	    if (isset($dbr)) continue;
	
	    $this->curri = $curri; unset($curri);
	    $t[$i] = wsal_21_1::addAnal($t[$i]);
	    
	    $dat[] = $t[$i]; unset($t[$i]);
	}
	
	if ($dat) {
	    $this->lcoll->insertMany($dat);
	    echo(count($dat) . ' lines loaded' . "\n");
	} else echo("no new lines\n");
	
	return;
    }
    
    private function lc20() {
	while ($ln = fgets($this->inh)) {
	    $i =  ++$this->curri;
	    $al = wsal_21_1::addAnal(false, $ln, $i);
	    $this->lcoll->insertOne($al);
	    echo($i . ' line loaded' . "\n");
	}
    }
    
    private function maxdb() {
	$group =   [[ '$group' => [ '_id'   => 'aggdat',
				    'max'   => ['$max' => '$i'],    ]]		];	
	$res = $this->lcoll->aggregate($group)->toArray();
	return $res[0]['max'];
    }
    
    private function setDMode() {
	global $argv;
	global $argc;
	if ($argc < 2) { $this->dmode = false; return; }
	foreach($argv as $a) if ($a === '-d') { $this->dmode = true; return; }
	$this->dmode = false;
    }
    
    private static function isProcessRunning($pidFile = self::pidf) {
	if (!file_exists($pidFile) || !is_file($pidFile)) return false;
	$pid = file_get_contents($pidFile);
	return posix_kill($pid, 0);
    }
    
    private static function setRun() {
	kwas(!self::isProcessRunning(), 'process already running');
	file_put_contents(self::pidf, getmypid());
    }
}

if (didCLICallMe(__FILE__)) new load_wsal_live();
