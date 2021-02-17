<?php

$file = '/var/log/apache2/other_vhosts_access.log';

$b = hrtime(1);
$t = filesize($file);
// $t = filemtime($file);
$e = hrtime(1);
echo number_format($e - $b) . "\n";
