<?php

require_once(__DIR__ . '/../load/load.php');
require_once('parse.php');
require_once('agent.php');

class wsal_cli {
    
    const outputStart = 0;
    const outputLineLimit = 100;
    
    public function __construct() {
	$this->locnt = 0;
	$this->read();
    }
    
    private function okish() {
	$c = $this->thea['httpcode'];
	$this->okish = $c >= 200 && $c <= 399;
    }
    
    private function doit() {
	$this->thea = wsal_parse::parse20($this->thel);
	$this->okish();
	$this->xref();
	$this->output();
    }
    
    private function read() {
	$r = $this->theinr = popen('tac' . ' ' . wsal_load::alpath . ' 2>/dev/null', 'r');
	while ($this->thel = fgets($r)) $this->doit();
	$this->exit();
    }
        
    private function exit() {
	echo $this->locnt . ' ' . 'total lines printed' . "\n";
	pclose($this->theinr);
	exit(0);	
    }
    
    private function output() {
	
	if (!$this->xref) return;
	if (!$this->okish) return;
	
	if ($this->locnt > self::outputLineLimit) { $this->exit(); }

	$this->out10();

    }
    
    private function out10() {
	
	$this->locnt++;
	if ($this->locnt < self::outputStart) return;
	
	$a = $this->thea;
	$s  = '';
	
	$s .= date('m/d H:i', $a['ts']);
	$s .= ' ';
	
	$s .= $a['ip'];
	$s .= ' ';
	
	$s .= $this->ref;
	$s .= ' ';
	// $s .= str_replace($this->ref, '', $this->thel);	

	$s .= $a['cmd'];
	$s .= ' ';
	$s .= wsal_agent::filter($a['agent']);
	
	$s = trim(preg_replace('/\s+/', ' ', $s));
	
	$s .= "\n";
	
	echo $s;

    }
    
    private function xref() {
	$l = $this->thel;
	$ref = $this->thea['ref'];
	$this->ref = $ref;
	$xref = true;
	if ($ref === '-') $xref = false;
	if (preg_match('/https?:\/\/[\S]*kwynn\.com/i', $ref)) $xref = false;
	$this->xref = $xref;
    }
}

if (didCLICallMe(__FILE__)) new wsal_cli();