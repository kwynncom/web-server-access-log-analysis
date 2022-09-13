<?php

require_once('/opt/kwynn/kwutils.php');

class remoteBashSession {

const pollims  = 50;
const pollius  = self::pollims * 1000;
const pollis   =	 0;
const timeoutS =	10;
const defaultLoginCmd = 'goa'; // goa is my name for ssh -[ipv] ubuntu@kwynn.com -i /path/loginKey.pem "$@"
const loginInitBytes = 100;
const followingPollms = 200;
const followingPollus = self::followingPollms * 1000;

public function __construct($loginCmd = self::defaultLoginCmd) { 
    $this->makeConn($loginCmd);
    $this->login();
}

private function makeConn($cmd) {
    $pdnonce = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $io;
    $this->procoh = proc_open($cmd, $pdnonce, $io); unset($pdnonce);
    $this->ouh  = $io[0];
    $this->inh  = $io[1]; unset($io);
}

private function login() {
    $this->getCmdResultI(self::loginInitBytes);		
}

public function getIn()  { return $this->inh; }
public function getOut() { return $this->ouh; }

public function __destruct() {
    fwrite	  ($this->ouh, 'exit' . "\n");
    fclose	  ($this->inh); 
    fclose	  ($this->ouh);
    proc_close($this->procoh);		
}

// 		$this->rbs->follow($this->gettc(), $this->liveh, $this->moo->getNew);
public function follow(string $cmd, $lhan, $moof, $newdf) {
    
    fwrite($this->ouh, $cmd . "\n");
    $h = $this->inh;
    $buf = '';
    $tow = false;
    while (is_resource($h) && !feof($h)) {
        $ra = [$h]; $ignorea1 = $ignorea2 = [];
        if (!stream_select($ra, $ignorea1, $ignorea2, 0, self::followingPollus)) continue;
        $ln = fgets($h);
        if (!$tow) { 
            $buf .= $ln;
            $att = $newdf($moof, $buf);
            if ($att) {
                $tow = true;
                if ($att && is_string($att)) {
                    kwas(fwrite($lhan, $att) === strlen($att), 'bad write follow wsal sync - init');
                    echo($att);
                }
            } else {
                kwynn();
            }
        }
        else {
            kwas(fwrite($lhan, $ln) === strlen($ln), 'bad write follow wsal - steady state');
            echo($ln);
        }
        
    } 		
}

private function getCmdResultI($stopwhen = null) {

        if		(is_integer ($stopwhen)) $sf = function($b, $swclo) { return isset($b[$swclo]); };
        else if (is_function($stopwhen)) $sf = $stopwhen;

        $h = $this->inh;
        $b = '';
        $ts = 0;
        $enterus = microtime(1);

        do {
                while (is_resource($h) && !feof($h)) {
                        $ra = [$h]; $ignorea1 = $ignorea2 = [];
                        if (!stream_select($ra, $ignorea1, $ignorea2, self::pollis, self::pollius)) break;
                        $c = fgets($h);
                        $b  .= $c;
                } 

                if ($sf && $sf($b, $stopwhen)) break;

        } while(microtime(1) - $enterus < self::timeoutS);

        return  $b;
}

public function getCmdRes($cmd, $f) {
        $cmd .= "\n";
        fwrite($this->ouh, $cmd);
        return $this->getCmdResultI($f);

}

}
