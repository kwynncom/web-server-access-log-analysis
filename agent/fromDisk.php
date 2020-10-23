<?php

require_once(__DIR__ . '/' . 'toDisk.php');

class wsal_ua_standalone_p10 {
    
    public $bigJSON;
    
    public static function get() {
	$o = new self();
	return $o->bigJSON;
    }
    
    private function __construct() { $this->load();  }
    
    private function load() {
	if (!isAWS()) $path = wsla_agent_sa30::path;
	else	      $path = __DIR__;
	$this->bigJSON      = file_get_contents($path);
    }
}

if (didCLICallMe(__FILE__)) { new wsal_ua_standalone_p10(); }