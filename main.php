<?php // The first version in GitHub was commented, or commented more.

require_once('load.php');
require_once('bots.php' );
require_once('output10.php');

class parse_web_server_access_logs {


    function __construct() {
	$this->load();
	// $this->f20();
	$this->f30();
	$this->f40();
	$this->f45();
	$this->f50();
	// $this->f60();
	// $this->f70();
	$this->f80();
	$this->save();
    }
    
    private function save() {
	
    }
    
    private function load() {
	$o = new wsal_load_and_parse();
	$this->a20 = $o->get();
    }
    
    public function get() { 
	return $this->a20;
    }
    
    private function f80() {
	// new wsal_output($this->a20);
    }
    
    private function f70($r) {
	return;
	
    }
    
    private function f60() {
	$hu = 0;
	$a = $this->a20;
	$tcnt = count($a);
	for ($i=0; $i < $tcnt; $i++) {	
	    $r = $a[$i];	
	    if (!isset($r['primeHu']) || !$r['primeHu']) continue;
	    $this->f70($r);
	    $hu++;
	    continue;
	}
	
	return;
    }
    
    private function f45() {
	$ia = [];
	$ipa = [];
	$a = $this->a20;

	$tcnt = count($a);
	
	$this->ipprn = 0;
	
	for ($i=0; $i < $tcnt; $i++) {	
	    $r = $a[$i];
	    
	    $maybeHu = false;

	    $ip = $r['ip'];
	    if (!isset($ia[$ip])) 
		       $ia[$ip] = 0;
	    ++$ia[$ip];
	    
	    if ($r['primeGet']) {
		if (!isset($ipa[$ip])) 
		           $ipa[$ip] = 0;
		
		$ipa[$ip]++;
		if (!$r['bot']) $maybeHu = true;
		$this->ipprn++;
	    }
	    
	    $r['maybeHu'] = $maybeHu;	    
	    $this->a20[$i] = $r;
	}
	
	$this->ipa['tot']   = $ia;
	$this->ipa['prime'] = $ipa;
   }
    
   private function maybeMe($agin, $rat, $ip) {
       $ags = ['(Linux; Android 5.0; SAMSUNG-SM-N900A)', '(X11; Linux x86_64',  '(Linux; Android 8.1.0; LM-X210(G))'];
       foreach($ags as $ag) if( strpos($agin, $ag) !== false) {
	   if ($rat > 0.05) return true;
       }
       return false;
    }
   
    private function f50() {
	$a = $this->a20;
	$huMaybeNotMe = 0;
	
	$tcnt = count($a);
	
	for ($i=0; $i < $tcnt; $i++) {
	
	    $r = $a[$i];
	    
	    if (!$r['maybeHu']) continue;
	    
	    $primeHuman = false;
	    $maybeMe = false;

	    $ipc = $this->ipa['prime'][$r['ip']];
	    $line = $r['line'];
	    $rat = $ipc / $this->ipprn;
	    $r['prrat'] = $rat;
	    $r['pruse'] = $ipc;
	    
	    $ag = $r['agent'];
	    if ($this->maybeMe($ag, $rat, $r['ip'])) {
	       $maybeMe = true;
	    } else {
		$primeHuman = true;
		$huMaybeNotMe++;
	    }
	    
	    

	    if ($rat < 0.01) {
		$lowusage = true;
	    } else if (!$maybeMe) {
		if (!isset($sneakBotT10[$ipc])) {
		           $sneakBotT10[$ipc] = true; // set per IP address if use this ; also look at speed of crawling
	
		}
	    }

	    if ($primeHuman) {
		$line = $r['line'];
		$ignore = 1;
	    }
	
	    
	    $r['maybeMe']    = $maybeMe;
	    $r['primeHu'] = $primeHuman;
	    
	    $this->a20[$i] = $r;
	}
	
	return;
    }
    
    
    private function f40() {
	$a = $this->a20;
	
	$notbotcnt = 0;
	$tcnt = count($a);
	
	for ($i=0; $i < $tcnt; $i++) {
	    $r = $a[$i];
	    
	    $htc = $r['httpcode'];
	    if ($htc >= 400 && $htc <= 499) 
		 $r['err'] = $htc;
	    else $r['err'] = 'OK';
	    
	    
	    $primeGet = false;
	    if (!in_array($r['ext'], ['ico', 'png', 'gif', 'css', 'js']) && $r['err'] === 'OK') {
		$primeGet = true;
	    }
	    
	    
	    $r['primeGet'] = $primeGet;
	    
	    $ag = $r['agent'];
	    if ($bn = isBot30($ag)) {
		if (!isset($bs[$bn])) {
		    $nada = 2;
		}
		$bs[$bn]  = true;
	    } else {
		$line = $r['line'];
		$notbotcnt++;
	    }
	    
	    
	    $r['bot'] = $bn;
	    $a[$i] = $r;
	}
	
	$this->a20 = $a;
	
	return;
    }
    
    private function f30() {
	
	$a = $this->a20;
	
	for ($i=0; $i < count($a); $i++) {
	    $r = $a[$i];
	    $cv  = $r['htCmdAndV'];
	    
	    $url = false;
	    
	    if ($cv !== '-') {
		$rev = strrev($cv);
		preg_match('/([\S]+)/', $rev, $mas);
		$cmd = strrev(substr($rev, strlen($mas[1])));
		$v = strrev($mas[1]);
		$ext = trim(pathinfo($cmd, PATHINFO_EXTENSION));
		preg_match('/([\S]+) ([\S]+)/', $rev, $mas);
		if (isset($mas[2])) $url = strrev($mas[2]);
	    } else {
		$cmd = '-';
		$v   = false;
		$ext = '';
	    }
	    
	    $r['cmd'] = $cmd;
	    $r['htv'] = $v;
	    $r['ext'] = $ext;
	    $r['url'] = $url;
	    
	    $this->a20[$i] = $r;
	}
	
	return;
    }
    

    

    
    // private function f20() { foreach($this->alla as $r) $this->a20[] = self::parseWSALLine($r); }
}

