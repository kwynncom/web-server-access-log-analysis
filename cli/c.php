<?php

require_once(__DIR__ . '/../load/load.php');
require_once('parse.php');

class wsal_cli {
    
    const outputLineLimit = 300;
    
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
    }
    
    private function exit() {
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
	$s  = '';
	$s .= $this->ref;
	$s .= ' ';
	// $s .= str_replace($this->ref, '', $this->thel);	
	$a = $this->thea;
	$s .= $a['cmd'];
	$s .= ' ';
	$s .= $a['agent'];
	$s .= "\n";
	
	echo $s;
	$this->locnt++;
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