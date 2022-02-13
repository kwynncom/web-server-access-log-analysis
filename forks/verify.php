<?php

class wsal_verify {
public function __construct ($db, $c, $f, $ts, $sz, $bpr, $epr, $isl) {
	$this->di05($sz);
	// $this->doDB10($db, $c, $ts, $bpr, $epr, $isl);
	// $this->fcmd($f, $n, $n, $isl);
} 

private function di05($sz) {
	$szd = number_format($sz);
	echo("$szd bytes\n"); unset($szd, $nd);	
}

private function dof20($f, $n, $dbn, $isl) {
	$c = $this->fcmd($f, $n, $dbn, $isl);
	echo("$c\n");
	$r = shell_exec(trim($c));
	echo(trim($r) . ' = file'. "\n");
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

private function doDB10($db, $c, $ts, $bpr, $epr, $isl) {

	$sq20 = "db.getCollection('$c')";
	$sq = "{'\$and' : [{'ftsl1' : $ts}, {'fp0' : {'\$gte' : $bpr}}, {'fp0' : {'\$lte' : $epr}}]}";
	$cq = $sq20 . '.count(' . $sq . ')';
	
	$dbn = dbqcl::q($db, $cq);
	$e10 = $dbn;
	if (!$isl) $e10 .= ' new';
	else       $e10 .= ' total';
	$e10 .= ' lines in database' . "\n";
	echo($e10); unset($e10);
	
	$lwq = "$sq20.find($sq)";
	$lwq .= ".sort({'fp0' : 1})";
	$lwq .= '.forEach(function(r) { print(r.line.trim()); })';

	$s = dbqcl::q($db, $lwq, false, false, true, ' | openssl md4 ', true);
	echo(trim($s) . ' = db' . "\n");
} // func

} // class
