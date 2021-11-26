<?php

class wsal_meta extends dao_wsal {
    
    const pends = 'pending';
    
    public function __construct() {
	parent::__construct(self::db);	
	$this->fcoll    = $this->client->selectCollection(self::db, 'files');
	$this->lcoll->createIndex(['n' => 1]);
    }
    
    private function ck($hl, $tl, $hds, $tds, $nin, $path) {
	
	$hr = $this->lcoll->count(['n' => 1   , 'line' => $hl]);
	$tr = $this->lcoll->count(['n' => $nin, 'line' => $tl]);
	
	if ($hr === 1 && $tr === 0) {
	    $fr = $this->fcoll->findOne(['headds' => $hds]);
	    if (!$fr) return self::pends;
	    $prevLines = $fr['lines'];
	    $ckTail = $nin - $prevLines + 1;
	    $cmd = "tail -n $ckTail $path 2> /dev/null | head -n 1";
	    $prl = trim(shell_exec($cmd));
	    $pra = wsal_parse::parse($prl);
	    if ($pra['dates'] === $fr['tailds']) return ['status' => 'partial', 'startAt' => $prevLines + 1];
	}
	
	$q = ['n'  => ['$gte' => 1, '$lte' => $nin],
	      'ts' => ['$gte' => strtotime($hds), '$lte' => strtotime($tds)]
	    ];
	$lr = $this->lcoll->count($q);
	if ($hr + $tr === 2 && $lr === $nin) return 'loaded';
	return self::pends;
    }
    
    public function rdAndCkFile($path) {
	$head  = self::ht('head', $path);
	$tail  = self::ht('tail', $path);
	$lines = self::lines($path);
	$headpa = wsal_parse::parse($head);
	$tailpa = wsal_parse::parse($tail);
	$headds = $headpa['dates']; unset($headpa);
	$tailds = $tailpa['dates']; unset($tailpa);
	$hts = strtotime($headds);
	$_id = date('m-d', $hts); unset($hts);
	$status = $this->ck($head, $tail, $headds, $tailds, $lines, $path); unset($head, $tail, $path);
	$qkey = ['_id' => $_id];
	if (is_array($status)) {
	    extract($status);
	    $dbdat = ['status' => $status];
	    $vars = get_defined_vars();
	} else $vars = $dbdat = get_defined_vars();
	$this->fcoll->upsert($qkey, $dbdat);
	
	if ($status === 'loaded') return true;
	return $vars;
    }
    
    public static function lines($path) { 
	kwas(file_exists($path), "$path does not exist");
	return intval(trim(shell_exec('wc -l < ' . $path)));   
    }
    
    public static function ht($cmd, $path, $n = 1) { 
	kwas($cmd === 'head' || $cmd === 'tail', 'heads or tails - ht meta 20');
	kwas(is_numeric($n), 'n must be numeric ht meta 20');
	$nc = intval($n); unset($n);
	kwas(file_exists($path), "$path does not exist");
	return trim(shell_exec("$cmd -n $nc " . $path));   
    }
}
