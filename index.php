<?php // The first version in GitHub was commented, or commented more.

require_once('parse.php');
require_once('bots.php' );

class parse_web_server_access_logs {

    const lpath  = '/tmp/access.log';
    const lafter = '2020-10-15 00:30';
    const max2explines = 45;

    function __construct() {
	$this->load();
	$this->f10();
	$this->f20();
	$this->f30();
	$this->f40();
	$this->f45();
	$this->f50();
    }
    
    private function f45() {
	$ia = [];
	$ipa = [];
	$a = $this->a20;

	$tcnt = count($a);
	
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
	    }
	    
	    $r['maybeHu'] = $maybeHu;	    
	    $this->a20[$i] = $r;
	}
	
	$this->ipa['tot']   = $ia;
	$this->ipa['prime'] = $ipa;
   }
    
   private function maybeMe($agin) {
       $ags = ['(Linux; Android 5.0; SAMSUNG-SM-N900A)', '(X11; Linux x86_64',  '(Linux; Android 8.1.0; LM-X210(G))'];
       foreach($ags as $ag) if( strpos($agin, $ag) !== false)  return true;
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

	    $ag = $r['agent'];
	    if ($this->maybeMe($ag)) {
	       $maybeMe = true;
	    } else {
		$primeHuman = true;
		$huMaybeNotMe++;
	    }
	    
	    $sneakBotT10 = false;
	    
	    $ipc = $this->ipa['prime'][$r['ip']];
	    $line = $r['line'];
	    if ($ipc < 10) {
		$lowusage = true;
	    } else if (!$maybeMe) {
		$sneakBotT10 = true;
		$primeHuman  = false;  // CAREFUL OF RATIOS!!!! - fixed number above !!!!
	    }

	    if ($primeHuman) {
		$line = $r['line'];
		$ignore = 1;
	    }
	
	    
	    $r['maybeMe']    = $maybeMe;
	    $r['primeHuman'] = $primeHuman;  // CAREFUL OF RATIOS!!!! - fixed number above !!!!
	    
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
	    if (!in_array($r['ext'], ['ico', 'png', 'gif', 'css', 'js']) && $r['err'] === 'OK') $primeGet = true;
	    
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
	    
	    if ($cv !== '-') {
		$rev = strrev($cv);
		preg_match('/([\S]+)/', $rev, $mas);
		$cmd = trim(strrev(substr($rev, strlen($mas[1]))));
		$v = strrev($mas[1]);
		$ext = pathinfo($cmd, PATHINFO_EXTENSION);
	    } else {
		$cmd = '-';
		$v   = false;
		$ext = '';
	    }
	    
	    $r['cmd'] = $cmd;
	    $r['htv'] = $v;
	    $r['ext'] = $ext;
	    
	    $this->a20[$i] = $r;
	}
	
	return;
    }
    
    private function load() {
	$t = trim(file_get_contents(self::lpath)); // trim prevents empty lines
	$a = explode("\n", $t);
	$cnt = count($a);
	$this->alla = $a; // all array
	return;
    }
    
    private static function ivc   ($nin)    { return intval(ceil($nin));} // intval ceiling
    private static function avg   (&$a, $b) { $a = self::ivc(($a + $b) / 2);} // for binary filter
    
    private function f10() { // binary date filter.  Assumes lines are in ascending date order.  Works in my one case.
	
	static	   $tsa = false; // timestamp after
	if (!$tsa) $tsa = strtotime(self::lafter);
	
	$a = $this->alla;
	$cnt = count($this->alla);
		
	$nxt = $cnt; // next try / next guess in binary search
	self::avg($nxt, 0);
	$imaxp = $cnt - 1; // max index possible
	$iminp = 0;
		
	for ($i=0; $i < self::max2explines; $i++) { // set a limit in case of error and infinite loop, see note at bottom
	    $p = parseWSALLine($a[$nxt]);
	    if ($p['ts'] >= $tsa) {
		$imaxp = $nxt;
		self::avg($nxt, $iminp);
	    }
	    else {
		$iminp = $nxt;
		self::avg($nxt, $imaxp);
	    }
	    
	    if ($imaxp === $nxt) break;
	}
	
	$a2 = [];
	for ($i=$nxt; $i < $cnt; $i++) $a2[] = $a[$i];
	
	$this->alla = $a2;
    }
    
    private function f20() { foreach($this->alla as $r) $this->a20[] = parseWSALLine($r); }
}

new parse_web_server_access_logs();
