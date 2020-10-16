<?php 

require_once('parse.php');

class parse_web_server_access_logs {

    const lpath  = '/tmp/access.log';
    const lafter = '2020-10-01'; // only include / parse after this date
    const max2explines = 45; // approximately 2^max2exp... limit on lines; see binary filter below

    function __construct() {
	$this->load();
	$this->f10(); // filter 10 - like BASIC lines: 10, 20, ...
	$this->f20();
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
    
    private function f20() {
	foreach($this->alla as $r) {
	    $p = parseWSALLine($r);
	    continue; // continue only for an active breakpoint line
	}
    }
}

new parse_web_server_access_logs();

/* Regarding the for loop limit on the binary search, a binary search should finish at most in the binary log of the number of lines.  
 * I had 271,000 lines before the filter and 29,000 afterward.  That took 18 iterations.  2^18 = 262,144.  2^45 is around 35 trillion.  
 * That should do in most cases.  */
