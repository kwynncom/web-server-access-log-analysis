<?php

require_once('remoteBash.php');
require_once(__DIR__ . '/../utils/parse.php');
require_once(__DIR__ . '/../utils/getLocalFile.php');
require_once('overlap.php');

class syncWSAL {
    
const testForSite = 'kwynn.com';
const siteifgt = 0.20; // sample log has 64% of lines with kwynn.com
const liveLineWindow = 200;
const rnm = '/var/log/apache2/access.log';
const mints = 1262954801; // 1262954801 === Fri Jan 08 2010 07:46:41 GMT-0500 (Eastern Standard Time)
const testOvP = '/tmp/logs';
const testOv = true;
const follow = true;
    
public function __construct() {
    $this->getLocalH();
    if (1) {
            $this->startRemote();
            $this->getRemoteH();
    }
    $this->createRunningFile();
    $this->sync();
}

private function sync() {
    $rl = intval(trim($this->rbs->getCmdRes('wc -l < ' . self::rnm, 1)));
    $ll = intval(trim(shell_exec('wc -l < ' . $this->livefp)));
    $d = $rl - $ll;
    kwas($d >= 0, 'remote lines less than local lines wsal');
    
    if ($d <= 0) {
        echo("No new lines\n");
        return;
        
    }
    
    
    $this->lock();
    
    $c  = $this->gettc($d, false);
    $c .= ' | bzip2 | openssl base64 ';
    $r = $this->rbs->getCmdRes($c, 30);
    $this->decomAndWrite($r);
    return;

}

private function gettc(int $d = 0, $follow = false) {
	$getn = self::liveLineWindow + $d;
	$c = "tail -n $getn ";
	if (self::follow && $follow) $c .= '-f ';
	$c .= self::rnm;
	return $c;
}

private function decomAndWrite($ct) {
    $bz = base64_decode($ct);
    $ot  = bzdecompress($bz);
    $t = $this->syncOverage($ot);

    fflush($this->liveh);
    flock ($this->liveh, LOCK_UN);
    fclose($this->liveh);
    $this->checkSum();
    
    
}

private function syncOverage($tin) {
	
	$t = $this->moo->getNew($tin);
	$szt = strlen($t);
    kwas(fwrite($this->liveh, $t) === $szt, 'write fail wsal');	
	if (self::follow) $this->follow();
}

private function follow() {
	if (0) {
	$cmd = $this->gettc(0, true);
	$inh = $this->rbs->getIn();
	$ouh = $this->rbs->getOut();
	
	fwrite($ouh, $cmd . "\n");
	
	while($l = fgets($inh)) {
		echo('RECEIVED: ' . $l);
		kwas(fwrite($this->liveh, $l) === strlen($l), 'bad follow write');
		echo('written' . "\n");
		
	}
	}
	
	$this->rbs->follow($this->gettc(0, true), $this->liveh, function ($t) { return $this->moo->getNew($t); });
}

private function checkSum() {
	
	static $cs1 = false;
	static $ci  = 0;
	
    $cmdl = 'openssl md5 ' . $this->livefp . ' | awk \'{print $2}\'';
    echo($cmdl . "\n");
    $lm = trim(shell_exec($cmdl));
	if (!$cs1) $cs1 = $lm;
	clearstatcache();
    $ls = filesize($this->livefp);
    $cmd = "head -c $ls " . self::rnm . ' | openssl md5 | awk \'{print $2}\' ';
    echo($cmd . "\n");
    $rm =  trim($this->rbs->getCmdRes($cmd, 30));
	if ($ci === 1) echo($cs1 . ' = starting checksum' . "\n");
    echo($lm . ' = local' . "\n");
    echo($rm . ' = remote' . "\n");
    kwas($lm === $rm, 'md5 mismatch wsal sync');
    echo('Match - OK!' . "\n");
	if ($ci === 1) {
		kwas($lm !== $cs1, '1 checksum should not be equal to 2nd - wsal');
		echo('OK - start does not equal finish' . "\n");
	}
	$ci++;
}

private function lock() {
    
    $this->checkSum();
    
	$this->moo = new manageOverlap();
	
	$ltls = shell_exec('tail -n 5 ' . $this->livefp);
	$this->moo->setCopy($ltls);
    $this->llla = explode("\n", $ltls);
    
    $this->liveh = fopen($this->livefp, 'a'); kwas($this->liveh, 'live file open fail wsal');
    $wb = 1;
    kwas(flock($this->liveh,  LOCK_EX | LOCK_NB, $wb), 'lock failed wsal'); kwas(!$wb, 'wsal would block fail');
}

private function startRemote() {    
    $this->rbs = new remoteBashSession();
}

private function testForSite() {
    $t = intval(trim(shell_exec('wc -l < ' . $this->lopath)));
    $s = intval(trim(shell_exec('grep ' . self::testForSite . ' ' . $this->lopath . ' | wc -l ')));
    if ($s / $t > self::siteifgt) $this->site = str_replace('.', '_', self::testForSite);
    else                          $this->site = '';
}


private function createRunningFile() {
    $this->testForSite();
    $p  = wsal_parse::parse($this->localH);
    $nf  = '';
    $nf .= dirname($this->lopath) . '/';
    $nf .= $this->site . ($this->site ? '_' : '') . 'begin_';
    $nf .=  date('Y_m_d_Hi_s_', $p['ts']);
    $nf .= sprintf('%06d', $p['usfri']);
    $nf .= '_live_access.log';
    
    $fe = file_exists($nf); 
    if (!$fe) {
        kwas(copy($this->lopath, $nf), 'copy to live wsal file failed');
        chmod($nf, 0600);
    }
    
    $this->livefp = $nf;
    
    return;
}

private function getRemoteH() {
    $rh = trim($this->rbs->getCmdRes('head -n 1 ' . self::rnm, 15));
    kwas($rh === $this->localH, 'head -n 1 does not match wsla files - local and remote');
    return;
}

private function getLocalH() {
	
	$f = getLLFile(self::testOv ? self::testOvP : '');
	
    $this->lopath = $l = $f;
    $cmdh = "head -n 1 $l";
    $this->localH = $h = trim(shell_exec($cmdh));
    return;
}
}

new syncWSAL();
