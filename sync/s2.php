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
const testOv = false;
const follow = false;
    
public function __construct() {
    $this->getLocalH();
    if (1) {
            $this->startRemote();
            $this->getRemoteH();
    }
    $this->createRunningFile();
    $this->sync();
    if (self::follow) $this->follow();
}

private function sync() {
    $rl = intval(trim($this->rbs->getCmdRes('wc -l < ' . self::rnm, 1)));
    $ll = intval(trim(shell_exec('wc -l < ' . $this->livefp)));
    $d = $rl - $ll;
    kwas($d >= 0, 'remote lines less than local lines wsal');

    $this->lock();
    
    if ($d <= 0) {
        echo("No new lines\n");
        return;
        
    }
    
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
}

private function follow() {
	$moof = new manageOverlap();
	
	$ltls = shell_exec('tail -n 5 ' . $this->livefp);
	$moof->setCopy($ltls);
	
	$this->rbs->follow($this->gettc(0, true), $this->liveh, $moof, function ($moof, $t) { return $moof->getNew($t, true); });
}

private function checkSum() {
	
    static $cs1 = false;
    static $ci  = 0;

    clearstatcache();    
    $ls = filesize($this->livefp);
        
    $cmdl =  "head -c $ls " . $this->livefp . ' | openssl md5 | awk \'{print $2}\' ';
    $lm = trim(shell_exec($cmdl));
    echo($lm . ' = local' . "\n");
    
    if (!$cs1) $cs1 = $lm;

    $cmd = "head -c $ls " . self::rnm . ' | openssl md5 | awk \'{print $2}\' ';
    $rm =  trim($this->rbs->getCmdRes($cmd, 30));
    echo($rm . ' = remote' . "\n");
    kwas($lm === $rm, 'md5 mismatch wsal sync');
    echo($cmdl . "\n");
    echo($cmd  . "\n");
    echo('OK - Match!' . "\n");
    if ($ci === 1) {
            echo($cs1 . ' = starting checksum' . "\n");
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
   
    $this->liveh = fopen($this->livefp, 'a'); kwas($this->liveh, 'live file open fail wsal');
    $wb = 1;
    try {
        kwas(flock($this->liveh,  LOCK_EX | LOCK_NB, $wb), 'lock failed wsal'); kwas(!$wb, 'wsal would block fail');
    } catch(Exception $ex) {
        echo('file lock failed' . "\n");
        exit(0);
    }
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
