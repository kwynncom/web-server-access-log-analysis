<?php

require_once('remoteBash.php');
require_once(__DIR__ . '/../load/utils/parse.php');

class syncWSAL {
    
const testForSite = 'kwynn.com';
const siteifgt = 0.20; // sample log has 64% of lines with kwynn.com
const liveLineWindow = 30;
const rnm = '/var/log/apache2/access.log';
const mints = 1262954801; // 1262954801 === Fri Jan 08 2010 07:46:41 GMT-0500 (Eastern Standard Time)
    
    
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
    
    $getn = $d + self::liveLineWindow;
    $c  = "tail -n $getn " . self::rnm;
    $c .= ' | bzip2 | openssl base64 ';
    $r = $this->rbs->getCmdRes($c, 30);
    $this->decomAndWrite($r);
    return;

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
    $ra = explode("\n", $tin);
    $ma = [];
    $ll = $this->llla;
    $ui1 = false;
    for($i = self::liveLineWindow - 1; $i >= 0; $i--) {
        for($j=count($ll) - 1; $j >= 0 ; $j--) {
            if ($ra[$i] === $ll[$j]) {
                $ma[] = $ra[$i];
                if ($ui1 === false) $ui1 = $i;
                
            }
            
            $ckv = self::cku($ma);
            if ($ckv) {
                $towa = array_slice($ra, $ui1 + 1);
                $this->write($towa);
                return;
            }
        }
    }
        
}

private function write($wa) {
    
    $t = '';
    foreach($wa as $r) if (trim($r)) $t .= $r . "\n";
    echo($t);
    kwas(fwrite($this->liveh, $t) === strlen($t), 'write fail wsal');
    

}

private function cku($a) {
    $cnt = count($a);
    if ($cnt < 2) return false;
    
    $ua = [];
    foreach($a as $r) {
        $ua[$r] = true;
        if (count($ua) > 1) return true;
    }
    
    return false;
    
}

private function checkSum() {
    $cmdl = 'openssl md5 ' . $this->livefp . ' | awk \'{print $2}\'';
    echo($cmdl . "\n");
    $lm = trim(shell_exec($cmdl));
    clearstatcache();
    $ls = filesize($this->livefp);
    $cmd = "head -c $ls " . self::rnm . ' | openssl md5 | awk \'{print $2}\' ';
    echo($cmd . "\n");
    $rm =  trim($this->rbs->getCmdRes($cmd, 30));
    echo($lm . ' = local' . "\n");
    echo($rm . ' = remote' . "\n");
    kwas($lm === $rm, 'md5 mismatch wsal sync');
    echo('Match - OK!' . "\n");
}

private function lock() {
    
    $this->checkSum();
    
    $this->llla = explode("\n", shell_exec('tail -n 5 ' . $this->livefp));
    
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
    $p  = wsal_parse_2022_010::parse($this->localH);
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
    $p = trim(file_get_contents('/var/kwynn/logpath.txt'));
    $cmdf = 'find ' . $p . ' -type f -printf "%T+\t%p\n" | sort -r | grep access | grep log | head -n 1 | awk \'{print $2}\'';
    $this->lopath = $l = trim(shell_exec($cmdf));
    
    $cmdh = "head -n 1 $l";
    $this->localH = $h = trim(shell_exec($cmdh));
    return;
}
}

// $o = 
// $o->getCmdRes('stat -c %s /var/log/apache2/access.log', function($b) { return preg_match("/\d+\n/", $b); });

// 

new syncWSAL();

