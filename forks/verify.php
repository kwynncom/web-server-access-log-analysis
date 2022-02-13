<?php

class wsal_verify {
public function __construct ($db, $c, $f, $n, $ts, $sz, $bpr, $epr) {
	$this->do10($db, $c, $f, $n, $ts, $sz, $bpr, $epr);
	
} // func

private function do10($db, $c, $f, $n, $ts, $sz, $bpr, $epr) {

	$szd = number_format($sz);
	$nd  = number_format($n);
	echo("$nd lines, $szd bytes\n"); unset($szd, $nd);
	
	$sq20 = "db.getCollection('$c')";
	$sq = "{'\$and' : [{'ftsl1' : $ts}, {'fp0' : {'\$gte' : $bpr}}, {'fp0' : {'\$lte' : $epr}}]}";
	$cq = $sq20 . '.count(' . $sq . ')';
	
	$dbn = dbqcl::q($db, $cq);
	
	$lwq = "$sq20.find($sq)";
	$lwq .= ".sort({'fp0' : 1})";
	$lwq .= '.forEach(function(r) { print(r.line.trim()); })';
	
	$tfn = '/tmp/wsqh_' . date('U');
	file_put_contents($tfn, $lwq);
	
	$mc = "mongo $db --quiet $tfn | openssl md4";
	
	
	// $a = dbqcl($c, $lwq);


	echo($mc . "\n");
	if (($pid = pcntl_fork()) === 0) {
		$s = shell_exec(trim($mc));
		echo(trim($s) . ' = db' . "\n");
		exit(0);
	} else $this->dof20($f, $n, $dbn);
	
	if ($pid !== 0) pcntl_waitpid($pid, $chpstatus);
	
} // func

private function dof20($f, $n, $dbn) {
	$c = $this->fcmd($f, $n, $dbn);
	echo("$c\n");
	$r = shell_exec(trim($c));
	echo(trim($r) . ' = file'. "\n");
}

private function fcmd($f, $n, $dbn) {
	$ism = $f === '/var/kwynn/mp/m/access.log'; // check fstab and $ mount eventually
	
	$dbl = $dbn < $n ? true : false;
	
	$c = '';
	if ($ism) $c .= 'goa "';
	$c .= "head -n " . ($dbl ? $dbn : $n) . ' ';
	if ($ism ) $c .= ' /var/log/apache2/access.log ';
	else	   $c .= $f;
	if ($n !== $dbn) {
		$c .= " | tail -n $dbn ";
	}

	$c .= ' | openssl md4 ';
	if ($ism) $c .= '"';
	
	return $c;
}

} // class
