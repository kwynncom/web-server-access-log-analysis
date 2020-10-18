<?php

require_once('/opt/kwynn/kwutils.php');

class wsal_load_and_parse {

    const lpath  = '/tmp/access.log';
    const lafter = '2020-10-15 19:30';
    const max2explines = 45;
    
    public function __construct() {
	$this->setFileDets();
	$this->filter();
	$this->load();
	$this->popArr();
	// $this->load();
    }
    
    public function get() {
	return $this->dla10;
    }
    
    private static function ivc   ($nin)    { return intval(ceil($nin));} // intval ceiling
    private static function avg   (&$a, $b) { $a = self::ivc(($a + $b) / 2);} // for binary filter
    
    private function getLine20($i) {

	$li = $this->ftlines - $i + 1;
	$c10 = "tail -n $li 2> /dev/null " . self::lpath . ' | head -n 1 ';
	$raw     = exec($c10, $output);	
	
	$trimmed = trim($raw);
	return $trimmed;
    }
    
    private function getLine($i) {
	$b = hrtime(1);
	$line = $this->getLine20($i);
	$e = hrtime(1);
	$d = intval(($e - $b) / 1000000);
	return $line;
    }
    
    private function filter() { // binary date filter.  Assumes lines are in ascending date order.  Works in my one case.
	
	static	   $tsa = false; // timestamp after
	if (!$tsa) $tsa = strtotime(self::lafter);
	
	$cnt = $this->ftlines;
		
	$nxt = $cnt; // next try / next guess in binary search
	self::avg($nxt, 0);
	$imaxp = $cnt - 1; // max index possible
	$iminp = 0;
		
	for ($i=0; $i < self::max2explines; $i++) { // set a limit in case of error and infinite loop, see note at bottom
	    
	    $ln = self::getLine($nxt);
	    
	    $p = self::getParsedArray($ln);
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
	
	$this->fstartAt = $nxt;
    }
    
    private function popArr() {

	$a = [];
	$i = $this->fstartAt;
	$line = strtok($this->filfile, "\n");
	while ($line) {
	    $a[] = self::getParsedArray($line, $i++, $this->lfile_md5);	 
	    $line = strtok("\n");
	}
	$this->dla10 = $a;	
    }
    
    private function setFileDets() {
	$this->lfile_size = filesize(self::lpath);
	$this->setFHash();
	$lns = intval(trim(shell_exec('wc -l < ' . self::lpath))); kwas($lns, 'no lines in file');
	$this->ftlines = $lns;
    }
    
    private function setFHash() {
	$md5 = trim(shell_exec('openssl md5 ' . self::lpath));	
	preg_match('/\=\s*([a-g0-9]{32})/', $md5, $ma); kwas(isset($ma[1]), 'bad md5');
	$this->lfile_md5 = $ma[1];
    }
    
    private function load() {
	$n  = $this->ftlines - $this->fstartAt + 1;
	$this->filfile = trim(shell_exec("tail -n $n " . self::lpath));
	return;
    }
    
    
private function getParsedArray($aWholeLine, $nin = false, $md5f = false) {

    kwas(trim($aWholeLine), 'there should not be any blank lines');
    kwas(    ($nin === false && $md5f === false)
	  || ($nin           && $md5f)
	    , 'bad getParsedArray mode');
    
    
    $lda = []; // line data array
 
    $lda['rline'] = $aWholeLine;
    $lda['lmd5']  = md5($aWholeLine);
    
    $lda['md5f'] = $md5f;
    
    $lda['n'] = $nin;

    $tln = $aWholeLine;    
    
    $ipre = '/[0-9A-Fa-f:\.]+/'; // IP address regular expression

    kwas(preg_match($ipre, $tln, $ipmatches) === 1, 'ip regex fail');
    $lda['ip'] = $ipmatches[0]; kwec('ip address', $ipmatches[0]); // kwec() === optional echo, defined at bottom

    $tln = substr($tln, strlen($lda['ip']) + 1); kwec('ip and space gone from temp line', $tln);

    if (substr($tln, 0, 4) === '- - ') { $tln = substr($tln, 4); kwec('user gone from temp line', $tln); }
    else die('user - - failed');

    $dateStr = substr($tln, 1, 26); kwec('date string', $dateStr);

    $lda['dateStr'] = $dateStr;

    $ts = strtotime($dateStr); kwec('UNIX epoch timestamp', $ts);

    $lda['ts']   = $ts;
    
    if (!$nin) return $lda;

    $tln = substr($tln, 29); kwec('date string gone', $tln); if ($tln[0] !== '"') die('" not found in expected place');

    kwas(preg_match('/\"[^\"]+\" /', $tln, $matchesHTTP) === 1, 'HTTP command match fail');

    $mlen = strlen($matchesHTTP[0]);
    $httpCmd = substr($tln, 1, $mlen - 3); 
    $lda['htCmdAndV'] = $httpCmd; kwec('http command', $httpCmd);
    $tln = substr($tln, $mlen); kwec('line after HTTP command', $tln);

    preg_match('/(\d+) (\d+)/', $tln, $matchesCodeAndLen); kwas(isset($matchesCodeAndLen[2]), 'HTTP code and length fail');

    $codeiv = intval($matchesCodeAndLen[1]);
    kwas($codeiv, 'ht code not int');
    
    $lda['httpcode']     = $codeiv;
    $lda['len']          = $matchesCodeAndLen[2]; kwec('HTTP return code and length of returned page', $matchesCodeAndLen);
    $tln = substr($tln, strlen($matchesCodeAndLen[0])); kwec('line after code and length', $tln);


    preg_match('/\"([^\"]*)\" /', $tln, $matchesEndOfLine); kwas(isset($matchesEndOfLine[1]), 'referrer match fail');

    $lda['ref'] = $matchesEndOfLine[1]; kwec('refering page', $matchesEndOfLine[1]);
    $tln = substr($tln, strlen($matchesEndOfLine[1]) + 4); kwec('line after referrer', $tln);

    kwas(isset($tln[1]), 'agent too short');

    $tln = substr($tln, 1, strlen($tln[1]) - 2); kwec('line after agent quotes ("...") removed', $tln);
    $lda['agent'] = $tln;

    $test = 'Mozilla/5.0 ';

    if (substr($tln, 0, strlen($test)) === $test)  $tln = substr($tln, strlen($test)); kwec('line after agent processing 1', $tln);

    $test = '(compatible; ';
    if (substr($tln, 0, strlen($test)) === $test)  $tln = substr($tln, strlen($test));

    $lda['agentp10'] = $tln; kwec('line after agent processing 2', $tln);
    
    return $lda;
}
}
function kwec() {  } // optional echo
