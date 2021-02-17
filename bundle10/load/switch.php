<?php

$do = 2;

switch($do) {
    case 0: require_once('msgRTest.php'); break;
    case 1: require_once('loadWorker.php'); new wsal_load_worker(); break;
    case 2: require_once('load.php')      ; new wsal_load(); break;
    case 3: require_once('lostRows.php')  ;  $o = new find_rows(); $o->search(); break; 
	
}