<?php

/*
$q = <<<'Q1'
mongo wsal --quiet --eval "db.getCollection('lines').find({ ftsl1 : 1644461682, fp0 : { $gte : 210726693 }, fpp1 : { $lte : 210727353  }}).sort({ fpp1 : 1}).forEach(function(r) { print(r.line.trim()); })" 
Q1; // does not work "not found"
 */

$q = 'mongo wsal --quiet --eval "db.getCollection(\'lines\').count()"'; // works

// $q = escapeshellarg(trim($q));
echo($q . "\n");
echo(shell_exec($q) . "\n");
