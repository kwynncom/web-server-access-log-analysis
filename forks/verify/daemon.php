<?php

$pipesInit = [
	0 => ['pipe', 'r'],
	1 => ['pipe', 'w']
	];

$md4p = proc_open('openssl md4', $pipesInit, $pipes);
fwrite($pipes[0], "hi3");
fclose($pipes[0]);
echo(fgets($pipes[1]));
fclose($pipes[1]);
proc_close($md4p);
