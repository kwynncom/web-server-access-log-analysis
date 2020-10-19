<?php

class wsal_pcontrol {
    
    const msgqid = 1;
    const msgTypeReadySend = 2;
    const maxsizePower = 8;
    
    public function __construct($isworker) {
	
	$mres = msg_get_queue(self::msgqid, 0600);
	$doser = 0;
	
	if (!$isworker) {
	    msg_receive($mres , self::msgTypeReadySend,$msgtype,pow(10, self::maxsizePower),$data,$doser, null,$err);
	    return;
	}
	else {
	    msg_send($mres, self::msgTypeReadySend, "from child", $doser );
	}
    }
}