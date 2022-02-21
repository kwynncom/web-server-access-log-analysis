<?php

require_once('/opt/kwynn/kwutils.php');
require_once(__DIR__ . '/' . 'parse.php');

interface wsal_db {
	const dbname = 'wsal';
	const colla   = 'lines';
	const lfin = '/var/kwynn/mp/m/access.log';
	// const lfin = '/var/kwynn/logs/a10K';

}