<?php

function colonIPv6($hin) {
    $res = '';
    
    for ($i=0; $i < 32; $i++) {
	$res .= $hin[$i];
	if ($i % 4 === 3 && $i !== 31) $res .= ':';
    }
    
    return $res;
}


function CIDRv6ToRange($prefix) { // see credit for basis at bottom

list($firstaddrstr, $prefixlen) = explode('/', $prefix);
$firstaddrbin = inet_pton($firstaddrstr);
$firstaddrhex = bin2hex($firstaddrbin);
$firstaddrstr = inet_ntop($firstaddrbin);
$flexbits = 128 - $prefixlen;
$lastaddrhex = $firstaddrhex;

$pos = 31;
while ($flexbits > 0) {

  $orig = substr($lastaddrhex, $pos, 1);
  $origval = hexdec($orig);
  $newval = $origval | (pow(2, min(4, $flexbits)) - 1);
  $new = dechex($newval);
  $lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);
  $flexbits -= 4;
  $pos -= 1;
}

$lastaddrbin = pack('H*', $lastaddrhex);
$lastaddrstr = inet_ntop($lastaddrbin);

$bytes = inet_pton($firstaddrstr);
$firsthex   = bin2hex($bytes);
$coloned    = colonIPv6($firsthex);

return $coloned . ' - ' . $lastaddrstr;

/* https://stackoverflow.com/questions/10085266/php5-calculate-ipv6-range-from-cidr-prefix
 * answered Apr 10 '12 at 9:44 by Sander Steffann */

} // end func

function packv4(&$in) {
    $cnt = preg_match_all('/\./', $in);
    
    kwas($cnt && intval($cnt) > 0, 'bad cidr v4 - match');

    for ($i=$cnt; $i < 3; $i++) {
	$in .= '.0';
    }
}

function CIDRv4ToRange($cidr) {
    $range = array();
    $cidr = explode('/', $cidr);
    kwas(isset($cidr[1]), 'invalid cidr v4');
    packv4($cidr[0]);
    $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
    $range[1] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
    return $range[0] . ' - ' . $range[1];
}

if (!function_exists('kwas')) {

function kwas($data = false, $msg = 'no message sent to kwas()') {
    if (!isset($data) || !$data) throw new Exception($msg);
}
}

