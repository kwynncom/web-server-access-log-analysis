<?php /* !!!! *****
 * NOTE TO SELF 2020/10/22 END OF DAY
 * FIND WHERE THE LINES MATCH, THEN DO AN MD5 OF THE COMMON PART - That should solve some problems.  
 * 
 * 
 */



class wsal_meta extends dao_wsal {
    public function __construct() {
	parent::__construct(self::db);	
	$this->fcoll    = $this->client->selectCollection(self::db, 'files');
	$this->batchatts = time();
	$this->farr = [];
	$this->tlines = 0;
	$this->pkqs   = [];
	$this->indexes(); // indexes
    }
    
    public function indexes() {
	$this->lcoll->createIndex(['bts' => 1]);
	$this->lcoll->createIndex(['ts'  => 1]);
	$this->lcoll->createIndex(['md5' => 1]);
    }
    
    public function confirm() {
	$end = time();
	$cnt = $this->lcoll->count(['bts' => ['$gte' => $this->batchatts, '$lte' => $end]]);
	if ($cnt !== $this->tlines) return;
	$dat['endts'] = $end;
	$dat['endr' ] = date('r', $end);
	$dat['status'] = 'OK';
	foreach($this->pkqs as $q) $this->fcoll->upsert($q, $dat);
	
	
	
    }
    
    public function checkSubset($fr) {
	
	
	
	return;
    }
    
    public function rdunz($path, $ext, $fidq, $i) {
	$path = str_replace($ext, '', $path);
	$lines = self::lines($path);
	$d1 = ['lines' => self::lines($path), 'md5' =>  self::md5($path), 'size' => filesize($path)];
	$d2 = self::getFirstLastLine($path);
	$this->farr[$i]['fl'] = $d2;
	// $this->farr[$i]['n']  = 
	
	$dat = array_merge($d1, $d2);
	$dat['bStartAtts'] = $this->batchatts;
	$dat['bStartAtR' ] = date('r', $this->batchatts);
	$this->fcoll->upsert($fidq, $dat);
	
	$this->checkSubset($d2);
	$this->tlines += $lines;
	
	// exit(0); // *** TESTING
	return;
    }
    
    private function check10($md5, $ext) {
	$res = $this->fcoll->findOne(['md5' . $ext => $md5, 'status' => 'OK']);
	if (!$res) return false;
	$p = ['projection' => ['n' => 1, '_id' => 0]];
	$l0  = $this->lcoll->findOne(['md5' => $res['line0md5']], $p);
	$ln  = $this->lcoll->findOne(['md5' => $res['linenmd5']], $p);	
	
	if (!isset($ln['n']) || !isset($l0['n'])) return false;
	if (       $ln['n'] -          $l0['n'] + 1 === $res['lines']) return true;
	
	$this->checkSubset($res);
    }
    
    public function rdAndCkFile($path, $ext, $beginDate, $batchSeq ) {
	$ext = str_replace('.', '', $ext);
	$md5 = self::md5($path);
	
	if ($this->check10($md5, $ext)) return true;
	
	$q   = ['md5' . $ext => $md5];
	$d2  = ['path' => $path, 'begints' => strtotime($beginDate), 'size' . $ext => filesize($path), 'beginS' => $beginDate, 'i' => $batchSeq];
	$dat = array_merge($q, $d2);
	$this->fcoll->upsert($q, $dat);
	$this->pkqs[] = $q;
	return $q;
	
	
	
	// $this->ilines =  wsalDateFilter::get(self::alpath, self::linesAfter);
	
	
    }
    
    public static function md5($path) {
	$cmd = "openssl md5 $path ";
	$res = trim(shell_exec($cmd));
	preg_match('/= ([a-g0-9]{32}$)/', $res, $m10); kwas(isset($m10[1]), 'md5 failed - rdAndCk 10');
	return $m10[1];
    }
    
    public static function lines($path) { return intval(trim(shell_exec('wc -l < ' . $path)));   }
    
    public static function linehash($lin) { return md5(trim($lin));  }
    
    public static function getFirstLastLine($path) {
	$line0 = trim(shell_exec("head -n 1 $path"));
	$line0md5 = self::linehash($line0);
	$linen = trim(shell_exec("tail -n 1 $path")); unset($path);
	$linenmd5 = self::linehash($linen); 
	return get_defined_vars();
    }
    
}