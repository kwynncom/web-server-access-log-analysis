<?php

class wsal_meta extends dao_wsal {
    public function __construct() {
	parent::__construct(self::db);	
	$this->fcoll    = $this->client->selectCollection(self::db, 'files');
    }
    
    private function ck($hl, $tl, $hds, $tds, $nin) {
	
	$hr = $this->lcoll->count(['n' => 1   , 'line' => $hl]);
	$tr = $this->lcoll->count(['n' => $nin, 'line' => $tl]);
	$q = ['n'  => ['$gte' => 1, '$lte' => $nin],
	      'ts' => ['$gte' => strtotime($hds), '$lte' => strtotime($tds)]
	    ];
	$lr = $this->lcoll->count($q);
	if ($hr + $tr === 2 && $lr === $nin) return 'loaded';
	return 'pending';
    }
    
    public function rdAndCkFile($path) {
	$head  = self::ht('head', $path);
	$tail  = self::ht('tail', $path);
	$lines = self::lines($path); unset($path);
	$headpa = wsalParseOneLine($head);
	$tailpa = wsalParseOneLine($tail);
	$headds = $headpa['dates']; unset($headpa);
	$tailds = $tailpa['dates']; unset($tailpa);
	$hts = strtotime($headds);
	$_id = date('m-d', $hts); unset($hts);
	$status = $this->ck($head, $tail, $headds, $tailds, $lines); unset($head, $tail);
	$dat = get_defined_vars();	
	$this->fcoll->upsert(['_id' => $_id], $dat);
	if ($status === 'loaded') return true;
	return $dat;
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
