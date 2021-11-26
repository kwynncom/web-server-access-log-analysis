<?php

require_once('/opt/kwynn/kwutils.php');
require_once('cidr.php');

class whois extends dao_generic {
    
    const db = 'whois';
 
    const netrlre = '[0-9A-Fa-f:\-\.* ]+(\/(\d+))*';
    const ipv6    = '[0-9A-Fa-f:]+';
    const ipv6rre = '/(' . self::ipv6 . ')\s*' . '\-\s*(' . self::ipv6 . ')/';
    const ipv4    = '\d+\.\d+\.\d+\.\d+';
    const ipv4rre = '/(' . self::ipv4 . ')\s*'   . '\-\s*(' . self::ipv4 . ')/';
    
    public function __construct() {
	$this->ipa = [];
	
	$this->db10();
	    $this->load();
	
	if (0) {

	    $this->p10();
	    $this->p20();
	}
	

    }
    
    private function p30($raw) {
	
      do {
	  if ($res = $this->doitARIN   ($raw)) break;
	  if ($res = $this->doitLANIC  ($raw)) break;
	  if ($res = $this->doitAfriNIC($raw)) break;	
	  if ($res = $this->doitRIPE1  ($raw)) break;	
      } while(0);
      
      return;
  }
  
      public function ipToArr($a) {
	
	$bytes = inet_pton($a);
	$rhex   = bin2hex($bytes);

	$allhex = str_pad($rhex, 32, '0', STR_PAD_LEFT);
	
	// echo $allhex . "\n";
	
	for ($i=3; $i >=0; $i--) {
	    $hex = substr($allhex,  8 * (3 - $i), 8);
   
	    $d = hexdec($hex);
	    
	    if ($d > PHP_INT_MAX) die("$d is too big an int");
	    
	    $da[$i] = $d;
	    // echo $d . "\n";
	}
	    
	return $da;

    }
    
  
      public function getIPArr($r) {
	
	if (strpos($r, '.')) $re = self::ipv4rre;
	else $re = self::ipv6rre;
	preg_match($re, $r, $matches);
	$ipa['l'] = self::ipToArr($matches[1]);
	$ipa['h'] = self::ipToArr($matches[2]);
	return $ipa;
	
    }

  
private function doitAfriNIC($raw) {
    try {
    $rexs = [
	['range'    => '/inetnum:\s+(' . self::netrlre . ')/'],
	['coname'  => "/person:\s+([^\n]+)/"],
	['netname'  => "/netname:\s+([^\n]+)/"],	
	['country' => '/country:\s+([A-Z]{2})/']
    ];
    
    foreach($rexs as $rea) {
	foreach($rea as $key => $re) {
	    preg_match_all($re, $raw, $matches);
	    
	    if (($key === 'netname' || $key === 'coname') && !isset($matches[1][0])) continue;
	    
	    kwas(isset($matches[1][0]), "key $key re $re doitLANIC match failed");
	    $x =  $matches[1][0];
	    
	    if ($key === 'range') {
		$deca = self::getIPArr($x);
		$hit['iparr'] = $deca;
	    }
		
	    $hit[$key] = $x;
	}
    }	    
   
    return $hit;
    
    } catch (Exception $e) { return false;    }  
}

private function doitRIPE1($raw) {
    try {
    // route6:         2001:b00::/29
    // descr:          Fastweb Networks ipv6 block
    // country:        IT
    //     const netrlre = '[0-9A-Fa-f:\-\.* ]+(\/(\d+))*';
    
    $rexs = [
	['cidr'    => '/route6:\s+([0-9A-Fa-f:]+\/\d+)/'],
	['coname'   => '/descr:\s+' . "([^\n]+)/"],
	['country' => '/country:\s+([A-Z]{2})/']
    ];
    
    foreach($rexs as $rea) {
	foreach($rea as $key => $re) {
	    preg_match_all($re, $raw, $matches);
	    kwas(isset($matches[1][0]), "key $key re $re doitRIPE1 match failed");
	    $x = $matches[1][0];
	    $hit[$key] = $x;
	    if ($key === 'cidr') {
		$range = CIDRv6ToRange($x);
		$hit['range'] = $range;
		$deca = self::getIPArr($range);
		$hit['iparr'] = $deca;
	    }
	}
    }

    return $hit;
    
    } catch (Exception $e) { return false;    }  
	
}

private function doitLANIC($raw) {
    try {
    $rexs = [
	['cidr'    => '/inetnum:\s+(' . self::netrlre . ')/'],
	['coname'  => "/owner:\s+([^\n]+)/"],
	['country' => '/country:\s+([A-Z]{2})/']
    ];
    
    foreach($rexs as $rea) {
	foreach($rea as $key => $re) {
	    preg_match_all($re, $raw, $matches);
	    kwas(isset($matches[1][0]), "key $key re $re doitLANIC match failed");
	    $x =  $matches[1][0];
	    
	    if ($key === 'cidr') {
		
		if (strpos($x, '.')) {
		    try { $range = CIDRv4ToRange($x); } catch (Exception $e) { continue; }
		}
		else $range = CIDRv6ToRange($x);
		
		if ($key === 'range') $range = $x;
		
		$hit['range'] = $range; // this is redundant in same cases; leave it be, or else
		$deca = self::getIPArr($range);
		$hit['iparr'] = $deca;
	    }
		
	    $hit[$key] = $x;
	}
    }	    
   
    return $hit;
    
    } catch (Exception $e) { return false;    }
    

}

private function doitARIN($raw) {
    
    try {
    
    $rexs = [
	['country' => '/Country:\s+([A-Z]{2})/'],
	['range'   => '/NetRange:\s+(' . self::netrlre . ')/'],
	[ 'cidr'   => '/CIDR:\s+(' .    self::netrlre . ')/'],
	['nname'   => '/NetName:\s+([^\s]+)/'],
	['parent'  => '/Parent:\s+([^\s]+)/'],
	['coname'  => "/(Customer|Organization):\s+([^\n]+)/"]
    ];
   
    foreach($rexs as $rea) {
	foreach($rea as $key => $re)
	{
	    preg_match_all($re, $raw, $matches);
	    
	    if (!isset($matches[1][0])) throw new Exception('cannot parse whois - ARIN');

	    $cnt = count($matches[0]);

	    for($i=0; $i < $cnt; $i++) {

		if ($key === 'range') {
		    $r = $matches[1][$i];
		    self::putres($key, $r, $cnt, $i, $hit);
		    $deca = self::getIPArr($r);
		    self::putres('iparr', $deca, $cnt, $i, $hit);
		    self::putres('country', $country, $cnt, $i, $hit);	
		}
		else if ($key === 'cidr')  {
		    
		    $m = $matches[1][$i];
		    $b = intval($matches[3][$i]);

		    self::putres('cidr', $m, $cnt, $i, $hit);
		    self::putres('bits', $b, $cnt, $i, $hit);		    
		}
		else if ($key === 'coname') {
		    
		    if (!isset($matches[1][$i])) throw new Exception('cannot parse whois - ARIN (coname)');
		    
		    $x = $matches[2][$i];
		    self::putres($key, $x, $cnt, $i, $hit);		    
		} else if ($key === 'country') {
		    $country = $matches[1][0];
	    
		}
		else  {
		    $x = $matches[1][$i];
		    self::putres($key, $x, $cnt, $i, $hit);
		}
	    }
	}
    }
        
    return $hit;
    } catch (Exception $ex) { return false; }

} // function   
    
    private function p20() {
	$ipas = $this->wcoll->find();
	foreach($ipas as $ipa) $this->p30($ipa['whois']);
	return;
    }
    
    private function db10() {
	parent::__construct(self::db);
	$this->wcoll    = $this->client->selectCollection(self::db, 'whois');
	$this->icoll    = $this->client->selectCollection(self::db, 'ips');
    }
    
    private function p10() {

	
	foreach($this->ipa as $ip) {
	    $ip = trim($ip);
	    
	    $ex = $this->wcoll->findOne(['ip' => $ip]);
	    if ($ex) continue;
	    
	    $whois = shell_exec("whois $ip");
	    $this->wcoll->insertOne(['ip' => $ip, 'whois' => $whois, 'whois_call_at' => time()]);
	    
	    continue;
	}
    }
    
    private function load() {
	$file = '/tmp/ip';
	$raw = shell_exec('head -n 5000 ' . $file);
	$a = explode("\n", $raw);
	
	foreach($a as $s) {
	    $ips = substr($s, 12);
	    preg_match('/^\S+/', $ips, $m);
	    if (isset($m[0])) {
		$q = ['_id' => $m[0]];
		$this->icoll->upsert($q, $q);
	    }
	}
    }
    
    private function putres($key, $val, $n, $i, &$target) {
    		    
    if ($n === 1) $target    [$key] = $val;
    else          $target[$i][$key] = $val;
}

    
}

if (didCLICallMe(__FILE__)) new whois();