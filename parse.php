<?php

require_once('/opt/kwynn/kwutils.php');

/* parseWSALLine() below  - Input is a line of a web server access log.
 * Output is an assoc array of the fields, plus an integer UNIX Epoch timestamp and some user agent processing. */

function parseWSALLine($aWholeLine) {
    
    $ipre = '/[0-9A-Fa-f:\.]+/'; // IP address regular expression
    
    if (!trim($aWholeLine)) die('there should not be any blank lines');

    $lda = []; // line data array
    
    $lda['line'] = $aWholeLine;

    $tln = $aWholeLine; kwec('whole line', $tln); // kwec() === optional echo, defined at bottom

    kwas(preg_match($ipre, $aWholeLine, $ipmatches) === 1, 'ip regex fail');
    $lda['ip'] = $ipmatches[0]; kwec('ip address', $ipmatches[0]);

    $tln = substr($aWholeLine, strlen($lda['ip']) + 1); kwec('ip and space gone from temp line', $tln);

    if (substr($tln, 0, 4) === '- - ') { $tln = substr($tln, 4); kwec('user gone from temp line', $tln); }
    else die('user - - failed');

    $dateStr = substr($tln, 1, 26); kwec('date string', $dateStr);

    $lda['dateStr'] = $dateStr;

    $ts = strtotime($dateStr); kwec('UNIX epoch timestamp', $ts);

    $lda['ts']   = $ts;

    $tln = substr($tln, 29); kwec('date string gone', $tln); if ($tln[0] !== '"') die('" not found in expected place');

    kwas(preg_match('/\"[^\"]+\" /', $tln, $matchesHTTP) === 1, 'HTTP command match fail');

    $mlen = strlen($matchesHTTP[0]);
    $httpCmd = substr($tln, 1, $mlen - 3); 
    $lda['httpCommand'] = $httpCmd; kwec('http command', $httpCmd);
    $tln = substr($tln, $mlen); kwec('line after HTTP command', $tln);

    preg_match('/(\d+) (\d+)/', $tln, $matchesCodeAndLen); kwas(isset($matchesCodeAndLen[2]), 'HTTP code and length fail');

    $lda['httpcode']     = $matchesCodeAndLen[1];
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
