<?php

function wsal_getIPsFromCLI() {

    $cmd = 'php ' . __DIR__ . '/../cli/c.php';
    $out = shell_exec($cmd);
    return $out;
}
