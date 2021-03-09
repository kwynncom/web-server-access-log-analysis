<?php

require_once('parse.php');

$s = file_get_contents('/tmp/us.log');
$a = explode("\n", $s);
foreach($a as $l) {
    if (!trim($l)) continue;
    wsal_parse::parse($l);
    
}
