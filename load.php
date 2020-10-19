<?php

require_once('dateFilter.php');

class wsal_load {
    const alpath  = '/tmp/access.log';
    const linesAfter = '2020-10-15 19:30:00';
    
    public function __construct() {
	$lia =  wsalDateFilter::get(self::alpath, self::linesAfter);
    }
}

if (didCLICallMe(__FILE__)) new wsal_load();
