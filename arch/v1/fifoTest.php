<?php

require_once('/opt/kwynn/kwutils.php');

$path = '/tmp/wsla_kw_f10';
kwas(file_exists($path) || posix_mkfifo($path, 0600), 'fifo create fail');

$pid = pcntl_fork(); 
if ($pid === 0) {
	kwas($r = fopen($path, 'w'), 'seq2 rand file open fail');
	kwas(flock($r, LOCK_EX),'seq2 rand file lock fail');
	file_put_contents($path, 'from child');
	flock($r, LOCK_UN);
	fclose($r);
} else {
    echo file_get_contents($path);
    // pcntl_waitpid($pid, $status);   

}


