<?php

class wsal_verify_fi {

public function __construct($f, $isl, $ca) {
	$this->set10($f, $ca['t'], $ca['cr'], $isl);
}

private function set10($f, $n, $dbn, $isl) {
	$this->ovars = get_defined_vars();
}

public function getHash() {
	extract($this->ovars);
	$c = $this->fcmd($f, $n, $dbn, $isl);
	echo("$c\n");
	$r = shell_exec(trim($c));
	echo(trim($r) . ' = file'. "\n");
	return $r;
}

private function fcmd($f, $n, $dbn, $isl) {
	$ism = $f === '/var/kwynn/mp/m/access.log'; // check fstab and $ mount eventually
	
	$dbl = $dbn < $n ? true : false;
	$headn = $dbl ? $dbn : $n;

	$c = '';
	if ($ism) $c .= 'goa "';
	if (!$isl) $c .= "head -n $n ";
	else $c .= 'openssl md4 ';
	if ($ism ) $c .= ' /var/log/apache2/access.log ';
	else	   $c .= $f;
	if ($n !== $dbn && $headn !== $dbn && !$isl) {
		$c .= " | tail -n $dbn ";
	}

	if (!$isl) $c .= ' | openssl md4 ';
	if ($ism) $c .= '"';
	
	return $c;
}

} // class
