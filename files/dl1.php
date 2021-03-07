<?php

require_once('dateFilter.php');

$ia = wsalDateFilter::get('/var/log/apache2/access.log', '2021-03-01 14:00');
var_dump($ia);
exit(0);
