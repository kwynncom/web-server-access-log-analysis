<?php

function isBot25($ag) {
    $key = 'Mozilla/5.0';
    if (substr($ag, 0, strlen($key)) !== $key) return true;
    return false;
}

function isBot30($ag) {
    
    $bs =  ['Mozilla/5.0', '-'];
    foreach ($bs as $b) if ($ag === $b) return 'precise equality: ' . $b;
    
    $fs = ['isBot10', 'isBot20', 'isBot25'];
    foreach($fs as $f) {
	$r = $f($ag);
	if ($r) return $r;
    }
    
    return false;
}

function isBot10($ag) {
    
    $bs = ['Jorgee', 'ZmEu', 'Dataprovider.com;)', 'http://notifyninja.com/monitoring', '360Spider', '(internal dummy connection)', 'PetalBot',
	    'SemrushBot', 'AhrefsBot', 'Googlebot', 'DotBot', 'Applebot', 'CensysInspect'];
    foreach($bs as $b) if (strpos($ag, $b) !== false) return $b;    
    return false;
}


function isBot20($ag) {

    $ag = strtolower($ag);

    $bs = ['bot', 'spider', 'index', 'crawler', 'zgrab', 'GRequests', 'Go-http-client', 'curl', 'Apache-HttpClient', 'HTTP Banner Detection', 
	   'python-requests' ];

    foreach($bs as $b) if (strpos($ag, strtolower($b)) !== false) return $b;
   
    return false;
}
