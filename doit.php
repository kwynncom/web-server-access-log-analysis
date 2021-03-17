<?php

require_once('/opt/kwynn/kwutils.php');
require_once('bots.php');
require_once('parse.php');
require_once('agent.php');
// require_once('updates.php');
require_once('dao.php');
require_once('myrows.php');

class wsal_21_1 {
    
    const linesPerBatch = 300;
    
    public  function __construct($ll = PHP_INT_MAX) { 
	$this->p10($ll);  
    }
    public  function getA()	   { return $this->biga; }
    private static function gold10($a)	   { return !$a['bot'] && !$a['iref'] && !$a['err'] && !$a['xiref'];  }
    
    public static function addAnal($ain, $lin = false, $iin = false) {
	
	if ($lin) {
	    $lar = [];
	    $lar['i'] = $iin; unset($ain, $iin);
	    $lar['line'] = trim($lin); unset($lin);
	} else $lar = $ain;
	
	kwas(isset($lar['i']), 'no i adda 1023');
	$i = $lar['i']; kwas($i && is_integer($i) && $i >= 1, 'improper i adda 1024'); unset($i);
		
	kwas(trim($lar['line']) === $lar['line'], 'only trimmed lines here - adda 1030');
	
	$l = $lar['line'];
	if (!isset($lar['md5'])) $lar['md5'] = md5($l);
	
	$a = array_merge(wsal_parse::parse($lar['line']), $lar);
	$a['bot'] = isBot($a['agent']);
	$a['iref'] = self::isIntRef($a['ref']);
	$a['xiref'] = ($a['iref'] && $a['ext'] === 'js') || self::f20($a);

	$a['err'] = $a['httpcode'] < 200 || $a['httpcode'] > 399;

	$a['gold10'] = self::gold10($a);
	
	$a['mine'] = self::isMyRow($a);
	
	$a['datv'] = dao_wsal::datv;
	
	return $a;
    }
    
    private static function isMyRow($a) {
	static $dao = false;
	
	if (!$dao) $dao = new dao_myip();
	return $dao->ismy($a['ip'], $a['agent'], $a['ts']);
    }
    
    private function p10($ll) {
	
	$dao = new dao_wsal();
	$lineaall = $dao->get($ll, self::linesPerBatch);

	foreach($lineaall as $linea) {
	    $i = $linea['i'];
	    $this->out($linea, $i);
	}
	return;
    }

    private static function filterJS($a) {
	static $js = [];
	if (!$js) $js = ['date', 'agent', 'bot', 'url', 'iref', 'ip', 'i', 'xiref', 'err', 'ref', 'gold10', 'mine'];
	foreach($a as $f => $ignore) if (!in_array($f, $js)) {
	    unset($a[$f]);
	}
	return self::filterJS20($a);
    }
    
    private static function filterJS20($a) {
	
	if ($a['mine']) $a['iref'] = $a['xiref'] = false;
	
	return $a;
	
    }
    
    private static function f20($a) {
	static $xfiles = false; 
	if (!$xfiles) $xfiles = ['/t/1/09/counter/c_img/Kwynn_counter_screenshot_2012_0614_2055.gif', '/valid-xhtml10.png', '/t/0/other/valid-xhtml10.png', 
	    '/t/5/02/html5_valid.jpg', '/favicon.ico'];
	if (in_array($a['url'], $xfiles)) return true;
	return false;
    }
   
    public static function isIntRef($rin) { return preg_match('/^https?:\/\/w?w?w?\.?kwynn\.com/', $rin, $ms) ? true : false; }
    
    private function out($a, $i) {
	
	$s  = '';
	$s .= $a['date'] = date('m/d H:i:s', $a['ts']);
	$s .= ' ';
	$s .= $a['cmd'] = self::cmd($a['cmd']);

	self::ex($a, $i);
	
	$s .= ' ';
	$s .= $a['agent'] = wsla_agent_p30::get($a['agent']);
	
	if (iscli() && 1) echo($i . ' ' . $s . "\n");
	
	if (!iscli() || 1) {
	    if (!isset($this->biga)) $this->biga = [];
	    $this->biga[] = self::filterJS($a);
	}
	

	return;	
    }
    

    
    private static function cmd($c) { $c = str_replace('GET ', '', $c); return $c;     }
    
    private static function ex($a, $i) {  	if ($i === 670) {	kwynn();  }   }
    
    public  static function htget() {
	if ( !isset ($_REQUEST['ll'])) return;
	$ll = intval($_REQUEST['ll']);
	$o = new self($ll);
	$a = $o->getA();
	header('Content-Type: application/json');
	echo(json_encode($a));
	exit(0);
	
    }

}

if (didCLICallMe(__FILE__)) new wsal_21_1();
else if (!iscli()) wsal_21_1::htget();