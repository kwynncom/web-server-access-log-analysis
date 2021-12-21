<?php

require_once('/opt/kwynn/kwutils.php');
// require_once('loadDB.php');

class load_wsal_live /* extends dao_wsal*/ {

    const basecmd = '/usr/bin/goa ';
	const logp    = '/var/log/apache2/access.log';
	const lookbackN = 200;

    
	public static function get($lnb, $lne) {
		$o = new self($lnb, $lne);
		return $o->getNewLines();
	}
	
	public function getNewLines()  { return $this->newLinesA; }
	
    public function __construct($lnb, $lne) {
		$this->lnb = $lnb;
		$this->lne = $lne;
		$this->newLinesA = [];
		$this->l02();
		$this->l05();
		return;
    }
    
    
    private function wc() { 
		return intval(trim(self::c_shell_exec('wc -l < /var/log/apache2/access.log')));  
	}
    
	private function l02() {
		$r = self::c_shell_exec('cat -n ' . self::logp . ' | head -n 1', false);
		kwas($r === $this->lnb['wholeLine'], 'line 1 mismatch loadlive');
		return;
	}
	
	public static function c_shell_exec($s, $exl = true) {
		$c  = '';
		if (!isaws()) $c .= self::basecmd . "'";
		$c .= $s;
		if ($exl) $c .= ' ' . self::logp;
		if (!isaws()) $c .= "'";
		return trim(shell_exec($c));
	}
	
    private function l10() {
		$maxdb = $this->lne['n'];
		$maxlv = $this->wc();
		$n10 = $maxlv - $maxdb;
		$n = $n10 + self::lookbackN;

		$ct  = 'cat -n ' . self::logp . ' | ';
		$ct .= "tail -n $n ";
		$s = self::c_shell_exec($ct, false);
		$len = strlen($s);

		return $s;
    }
    
    private function l05() {
		$lss = $this->l10();
		$this->setNewLines($lss);
    }
     
    private function setnewLines($lss) { 
		$t = [];
		$lsa = explode("\n", $lss); unset($lss);
		$len = count($lsa);
		$dat = [];

		$cln = self::normnnl($this->lne['wholeLine']);
		for ($i=0; $i  < $len; $i++) {

			$l = self::normnnl($lsa[$i]);
			if ($l === $cln) {
				$this->newLinesA = array_slice($lsa, $i + 1);
				return;
			}
		}
	
	
	return;
    }
    
	private static function normnnl($lin) {
		$o = trim(preg_replace(wsal_parse::catnre, '$1 ', $lin));
		return $o;
	}
	


}

if (didCLICallMe(__FILE__)) new load_wsal_live();
