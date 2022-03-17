<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/utils/' . 'parse.php');
require_once('utils/utils.php');
require_once('utils/lock.php');

interface wsal_config {
	const dbname = 'wsal';
	const colla   = 'lines';
	const liveLocalName = '/var/log/apache2/access.log';
	const liveMount = '/var/kwynn/mp/m/access.log';
	const lfin      = self::liveMount;
	// const lfin = '/var/kwynn/logs/a2_500M';
	
	const nchunks =   4000;
	const chunks  = 500000;
	
	const splitat = 100000;
}