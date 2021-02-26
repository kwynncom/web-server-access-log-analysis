<?php

require_once('/opt/kwynn/kwutils.php');

$rbp = '/tmp/log/';
$base = '/var/kwynn/goa'; // see below

if (rcmd("'[ ! -d /tmp/log ] && echo create'", true) === 'create') rcmd("'mkdir /tmp/log'");
rcmd("'chmod 700 /tmp/log'");
$newf = 'access_' . date('Y_md_Hi_s') . '.log';
$newp = '/tmp/log/' . $newf;

rcmd('cp /var/log/apache2/access.log' . ' ' . $newp);
rcmd("chmod 600 $newp");
rcmd("bzip2 $newp");
$newp .= '.bz2';
$newf .= '.bz2';
rcmd("chmod 400 $newp");
shell_exec('/var/kwynn/scpal10 ' . $rbp . ' ' . $newf);

function rcmd($cmd, $doret = false) {
    global $base;
    
    return tse("$base " . $cmd, $doret);
}

function tse($cmd, $doret) { 
    $r = trim(shell_exec($cmd)); 
    if (!$doret) kwas(!$r, 'ERROR: ' . $cmd . ' failed with output ' . $r . "\n");
    return $r;
}

/* "goa" is something to the effect of the following, with execution permission bit set:
ssh -4 remote_user@example.com -i /home/local_user/private-key-to-given-remote-machine.pem "$@"
#END SCRIPT
 * 
 * The above executes remote commands
*/