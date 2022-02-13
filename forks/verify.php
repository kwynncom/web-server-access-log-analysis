<?php

class wsal_verify {
public function __construct ($db, $c, $f, $n, $ts, $sz) {
	$this->do10($db, $c, $f, $n, $ts, $sz);
	
} // func

private function do10($db, $c, $f, $n, $ts, $sz) {

	$szd = number_format($sz);
	$nd  = number_format($n);
	echo("$nd lines, $szd bytes\n"); unset($szd, $nd);
	
	$q = '';
	$q .= 'mongo wsal --quiet -eval ';
	$q .= <<<Q0024
"db.getCollection('$c').find({'ftsl1' : $ts}).sort({'fpp1' : 1}).limit($n).forEach(function(r) { print(r.line.trim()); });" | openssl md4
Q0024;

	echo($q . "\n");
	if (($pid = pcntl_fork()) !== 0) {
		$s = shell_exec(trim($q));
		echo(trim($s) . ' = db' . "\n");
		exit(0);
	} else $this->dof20($f, $n);
		
	
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
