<?php

require_once('/opt/kwynn/kwutils.php');


class test {
    public function __construct() {    }
    
    public function __destruct() { 
        echo('in destruct v10' . "\n");
    }

}

$o = new test();
kwas(pcntl_signal(SIGINT, 'kwynn'), 'did not set SIG');

sleep(5);
echo('exit at end - v10' . "\n");
