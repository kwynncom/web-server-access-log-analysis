<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/' . 'parse.php');
require_once('utils.php');
require_once('lock.php');

interface wsal_config {
	const dbname = 'wsal';
	const colla   = 'lines';
	const lfin = '/var/kwynn/mp/m/access.log';
	// const lfin = '/var/kwynn/logs/a10K';
	
	const nchunks =   4000;
	const chunks  = 500000;
}