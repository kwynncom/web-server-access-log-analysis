<?php

class wsal_verify {
public function __construct ($db, $c, $f, $n, $ts, $sz, $bpr, $epr) {
	$this->do10($db, $c, $f, $n, $ts, $sz, $bpr, $epr);
	
} // func

private function do10($db, $c, $f, $n, $ts, $sz, $bpr, $epr) {

	$szd = number_format($sz);
	$nd  = number_format($n);
	echo("$nd lines, $szd bytes\n"); unset($szd, $nd);
	
	$sq = "{'\$and' : [{'ftsl1' : $ts}, {'fp0' : {'\$gte' : $bpr}}, {'fp0' : {'\$lte' : $epr}}]}";
	
	$lwq = <<<LWQ
mongo --quiet --eval "db.getCollection('$c').find({'\$and' : [{'ftsl1' : $ts}, {'fp0' : {'\$gte' : $bpr}}, {'fp0' : {'\$lte' : $epr}}]})
LWQ;
	$lwq = trim($lwq);
	$lwq .= '.forEach(function(r) { print(r.line.trim()); });" | openssl md4';
	
	// $a = dbqcl($c, $lwq);


	echo($lwq . "\n");
	if (($pid = pcntl_fork()) === 0) {
		$s = shell_exec(trim($lwq));
		echo(trim($s) . ' = db' . "\n");
		exit(0);
	} else $this->dof20($f, $n);
	
	pcntl_waitpid($pid, $chpstatus);
	
} // func

private function dof20($f, $n) {
	$c = $this->fcmd($f, $n);
	echo("$c\n");
	$r = shell_exec(trim($c));
	echo(trim($r) . ' = remote'. "\n");
}

private function fcmd($f, $n) {
	if ($f === '/var/kwynn/mp/m/access.log') {
		 
$c = <<<REMCMD
goa "head -n $n /var/log/apache2/access.log | openssl md4"
REMCMD;
	return $c;

}
	// add check of /etc/fstab and $ mount
	return "openssl md5 $f";
}

} // class
