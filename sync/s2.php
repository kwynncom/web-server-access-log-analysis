<?php

require_once('remoteBash.php');

$o = new remoteBashSession();
// $o->getCmdRes('stat -c %s /var/log/apache2/access.log', function($b) { return preg_match("/\d+\n/", $b); });

// find [path] -type f -printf "%T+\t%p\n" | sort -r | grep access.log | head -n 1 | awk '{print $2}'
