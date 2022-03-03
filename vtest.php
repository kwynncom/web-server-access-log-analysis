<?php

require_once('/opt/kwynn/kwutils.php');

$s = "123.456.374.245 31/Oct/2022 333333";
//  unpack(string $format, string $string, int $offset = 0): array|false

// P 	unsigned long long (always 64 bit, little endian byte order)

// $t = 
for($i=0; true; $i  += 8) {
	if (isset($s[$i +  8])) {
		
		// print_r(unpack('P', $s, $i));
	}
	else break;
}

print_r(unpack('P*', $s));