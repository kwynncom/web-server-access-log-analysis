<?php
function wsalParseOneLine($aWholeLine, $tsonly = false) {

    kwas(trim($aWholeLine), 'there should not be any blank lines');
    
    $lda = []; // line data array
 
    $tln = $aWholeLine;    
    
    $ipre = '/[0-9A-Fa-f:\.]+/'; // IP address regular expression

    kwas(preg_match($ipre, $tln, $ipmatches) === 1, 'ip regex fail');
    $lda['ip'] = $ipmatches[0]; kwec('ip address', $ipmatches[0]); // kwec() === optional echo, defined at bottom

    $tln = substr($tln, strlen($lda['ip']) + 1); kwec('ip and space gone from temp line', $tln);

    if (substr($tln, 0, 4) === '- - ') { $tln = substr($tln, 4); kwec('user gone from temp line', $tln); }
    else die('user - - failed');

    $dateStr = substr($tln, 1, 26); kwec('date string', $dateStr);

    $ts = strtotime($dateStr); kwec('UNIX epoch timestamp', $ts);
   
    if ($tsonly) return $ts;

    $lda['dateStr'] = $dateStr;
    $lda['ts']   = $ts;
    $lda['rline'] = $aWholeLine;
    $lda['lmd5']  = md5($aWholeLine);
    
    $tln = substr($tln, 29); kwec('date string gone', $tln); if ($tln[0] !== '"') die('" not found in expected place');

    kwas(preg_match('/\"[^\"]*\" /', $tln, $matchesHTTP) === 1, 'HTTP command match fail');

    $mlen = strlen($matchesHTTP[0]);
    $httpCmd = substr($tln, 1, $mlen - 3); 
    $lda['htCmdAndV'] = $httpCmd; kwec('http command', $httpCmd);
    $tln = substr($tln, $mlen); kwec('line after HTTP command', $tln);

    preg_match('/(\d+) (\d+)/', $tln, $matchesCodeAndLen); kwas(isset($matchesCodeAndLen[2]), 'HTTP code and length fail');

    $codeiv = intval($matchesCodeAndLen[1]);
    try {
    kwas($codeiv, 'ht code not int');
    } catch(Exception $ex) {
	$blah = 1;
    }
    
    $lda['httpcode']     = $codeiv;
    $lda['len']          = $matchesCodeAndLen[2]; kwec('HTTP return code and length of returned page', $matchesCodeAndLen);
    $tln = substr($tln, strlen($matchesCodeAndLen[0])); kwec('line after code and length', $tln);


    preg_match('/\"([^\"]*)\" /', $tln, $matchesEndOfLine); kwas(isset($matchesEndOfLine[1]), 'referrer match fail');

    $lda['ref'] = $matchesEndOfLine[1]; kwec('refering page', $matchesEndOfLine[1]);
    $tln = substr($tln, strlen($matchesEndOfLine[1]) + 4); kwec('line after referrer', $tln);

    kwas(isset($tln[1]), 'agent too short');

    $tln = substr($tln, 1, strlen($tln[1]) - 2); kwec('line after agent quotes ("...") removed', $tln);
    $lda['agent'] = $tln;

    $test = 'Mozilla/5.0 ';

    if (substr($tln, 0, strlen($test)) === $test)  $tln = substr($tln, strlen($test)); kwec('line after agent processing 1', $tln);

    $test = '(compatible; ';
    if (substr($tln, 0, strlen($test)) === $test)  $tln = substr($tln, strlen($test));

    $lda['agentp10'] = $tln; kwec('line after agent processing 2', $tln);
    
    return $lda;
}

function kwec() {  } // optional echo
