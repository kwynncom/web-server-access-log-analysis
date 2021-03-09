<?php

require_once('parse.php');

$s = file_get_contents('/tmp/log/access_2021_0309_0134_59.log');
$a = explode("\n", $s);
foreach($a as $l) {
    if (!trim($l)) continue;
    wsal_parse::parse($l);
    
}
