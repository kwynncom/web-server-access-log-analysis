<?php

class wsal_pcontrol {
    
    const msgTypeReadySend = 2;
    const maxsizePower = 5;
    
    private function assembleArr($json, $start) {
	$a = json_decode($json,1);
	
	foreach($a as $r){
	    $n = $r['n'];
	    $d = $n - $start;
	    kwas($d >= 0, 'bad index startAt');

	    $this->assa[$d] = $r;
	}
	
    }
    
    public function get() { return $this->assa; }
    
    public function __construct($quid, $isworker, $datin = false, $cpus = 0, $startAt = 0) {
	
/*	if (msg_queue_exists($this->msgqid) && 0) { // messages will be kept for hours!!!!!
	   $ignore = 2;   
	   $mres = msg_get_queue($this->msgqid, 0600);
	   kwas(msg_remove_queue($mres), 'msg remove fail');
	} */
	
	$mres = msg_get_queue($quid, 0600);
	$doser = 0;
	
	if (!$isworker) {
	    for($i=0; $i < $cpus; $i++) {
		msg_receive($mres , self::msgTypeReadySend,$msgtype,intval(pow(10, self::maxsizePower)),$data,$doser, null,$err);
		$this->assembleArr($data, $startAt);
	    }
	    return;
	}
	else {
	    $str = json_encode($datin);
	    $datin['a'] = $datin;
	    $datin['ppid'] = $quid;
	    // $datin
	    file_put_contents('/tmp/child', json_encode($datin));
	    msg_send($mres, self::msgTypeReadySend, $str, $doser );
	}
    }
}