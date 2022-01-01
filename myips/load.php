<?php

require_once('/opt/kwynn/kwutils.php');

// dbqcl {
//	public static function q($db, $q = false, $exf = false, $cmdPrefix = '')

class myips {
	public static function load() {
		$res = dbqcl::q('qemail', false, '/var/kwynn/ipq10.js', 'goa');
		return;
	}
}

if (didCLICallMe(__FILE__)) myips::load();