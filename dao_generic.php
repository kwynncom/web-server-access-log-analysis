<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '/opt/composer');
require_once('vendor/autoload.php');
unset($__composer_autoload_files, $autolr); // I unset $__composer... because (often) I am trying to keep a very clean set of active variables. 

class kw3moncli extends MongoDB\Client {
    public function __construct() { parent::__construct('mongodb://127.0.0.1/', [], ['typeMap' => ['array' => 'array','document' => 'array', 'root' => 'array']]); }
    public function selectCollection     ($db, $coll, array $ignored = []) { return new kw3mondbcoll($this->getManager(), $db, $coll, ['typeMap' => ['array' => 'array','document' => 'array', 'root' => 'array']]); }
}

class kw3mondbcoll extends MongoDB\Collection {
    public function upsert($q, $set) {
		$r = $this->updateOne($q, ['$set' => $set], ['upsert' => true]);
		$sum  = 0; 	$sum += $r->getUpsertedCount(); $sum += $r->getModifiedCount(); $sum += $r->getMatchedCount();	
		kwas($sum >= 1, "kw3mondbcoll upsert sum = $sum when should be >= 1");
		return $r;
    }
}

class dao_generic_3  {
    
    private $dbname;
    private $client;
    
    protected function __construct($dbname) {
		$this->dbname = $dbname;
		$this->client = new kw3moncli();
    }
	
	protected function creTabs($ts) {
		foreach($ts as $k => $t) {
			$v = $k . 'coll';
			$this->$v = $this->client->selectCollection($this->dbname, $t);
		}	
    }
	
} // class
