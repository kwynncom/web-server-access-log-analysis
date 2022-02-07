<?php

function parse_line_file($h) {
	$i = 0;
	$ip = '';
	do { 
		$c = fgetc($h); $i++;
		if ($c === ' ') break;
		else $ip .= $c; 
	} while($i < 50);
	exit(0);
	
}