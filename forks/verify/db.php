<?php

class wsal_verify_db {

public function 
	__construct ($db, $c, $ts, $bpr, $epr, $isl) {
	$this->set10($db, $c, $ts, $bpr, $epr, $isl);

} // func

private function setCounts() {
	$this->crc();
	$this->sett ($this->db, $this->coll, $this->ts);
	// 
}

public function getCounts() { 
	$this->setCounts();
	return ['t' => $this->dbnt, 'cr' => $this->dbncr]; 
}

private function sett($db, $c, $ts) {
	if (isset($this->dbnt)) return;
	$q = "db.getCollection('$c').count({'ftsl1' : $ts})";
	$this->dbnt = dbqcl::q($db, $q);
}

private function crc() {
	$this->dbncr = $dbncr = dbqcl::q($this->db, $this->cq);
	
	if ($this->isl) $this->dbnt = $dbncr;
	
	$e10 = $dbncr;
	if (!$this->isl) $e10 .= ' new';
	else       $e10 .= ' total';
	$e10 .= ' lines in database' . "\n";
	echo($e10); unset($e10);	
}

public function getHash($vf) {
	$db = $this->db;
	$s = dbqcl::q($db, $this->lwq, false, false, true, ' | openssl md4 ', true);
	echo(trim($s) . ' = db' . "\n");
	file_put_contents($vf . '_d', $s);
	return $s;
}

private function set10($db, $c, $ts, $bpr, $epr, $isl) {
	
	$this->db  = $db;
	$this->isl = $isl;
	$this->coll = $c;
	$this->ts   = $ts;
	
	$sq20 = "db.getCollection('$c')";
	$sq30 = "{'\$and' : [{'ftsl1' : $ts}, {'fp0' : {'\$gte' : $bpr}}, {'fp0' : {'\$lte' : $epr}}]}";
	$this->cq = $sq20 . '.count(' . $sq30 . ')';

	$lwq = "$sq20.find($sq30)";
	$lwq .= ".sort({'fp0' : 1})";
	$lwq .= '.forEach(function(r) { print(r.line.trim()); })';
	
	$this->lwq = $lwq;
} // func
} // class
