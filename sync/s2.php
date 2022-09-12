<?php

require_once('remoteBash.php');
require_once(__DIR__ . '/../load/utils/parse.php');

class syncWSAL {
    
const testForSite = 'kwynn.com';
const siteifgt = 0.20; // sample log has 64% of lines with kwynn.com
const liveLineWindow = 200;
    
    
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
    $rl = intval(trim($this->rbs->getCmdRes('wc -l < /var/log/apache2/access.log', 1)));
    $ll = intval(trim(shell_exec('wc -l < ' . $this->livefp)));
    $d = $rl - $ll;
    kwas($d >= 0, 'remote lines less than local lines wsal');
    return;

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
    $rh = trim($this->rbs->getCmdRes('head -n 1 /var/log/apache2/access.log', 15));
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

