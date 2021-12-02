<?php

require_once('/opt/kwynn/kwutils.php');
// require_once('loadDB.php');

class load_wsal_live /* extends dao_wsal*/ {

    const basecmd = '/usr/bin/goa ';
	const logp    = '/var/log/apache2/access.log';
    const pidf    = '/tmp/lld_load_live_d_wsal_kwynn_com_ns2021_03_1.pid';
	const lookbackN = 200;
    
	public static function get($lnb, $lne) {
		new self($lnb, $lne);

	}
	
    public function __construct($lnb, $lne) {
		
		$this->lnb = $lnb;
		$this->lne = $lne;
		
	// parent::__construct();
	self::setRun();
	$this->setDMode();
	$this->l02();
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
    
	private function l02() {
		$r = self::c_shell_exec('head -n 1');
		kwas($r === $this->lnb['wholeLine'], 'line 1 mismatch loadlive');
		return;
	}
	
	public static function c_shell_exec($s, $exl = true) {
		$c  = self::basecmd . "'";
		$c .= $s;
		if ($exl) $c .= ' ' . self::logp;
		$c .= "'";
		return trim(shell_exec($c));
	}
	
    private function l10() {
	$maxdb = $this->lne['n'];
	$maxlv = $this->wc();
	$n10 = $maxlv - $maxdb;
	$n = $n10 + self::lookbackN;
	
	$ct  = 'cat -n ' . self::logp . ' | ';
	$ct .= "tail -n $n ";
	$s = self::c_shell_exec($ct, false);
	$len = strlen($s);
	
	return $s;
    }
    
    private function l05() {
	$lss = $this->l10();
	$this->match($lss);
    }
     
    private function match($lss) { 
	$t = [];
	$lsa = explode("\n", $lss); unset($lss);
	$len = count($lsa);
	$dat = [];
	
	$cup = false;
	$cln = $this->lne['n'] . ' ' . $this->lne['wholeLine'];
	for ($i=0; $i  < $len; $i++) {
	    
	    $l = self::normnnl($lsa[$i]);
	    if (!$l) continue; // should be written into dao, too
		if (!$cup) {
			$cup = $l === $cln;
			continue;
		}
	    	    
		$t[] = $l;
	}
	
	
	return;
    }
    
	private static function normnnl($lin) {
		$o = trim(preg_replace('/^\s*(\d+)\s+/', '$1 ', $lin));
		return $o;
	}
	
    private function lc20() {
	while ($ln = fgets($this->inh)) {
	    $i =  ++$this->curri;
	    $al = wsal_21_1::addAnal(false, $ln, $i);
	    $this->lcoll->insertOne($al);
	    echo($i . ' line loaded' . "\n");
	}
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
