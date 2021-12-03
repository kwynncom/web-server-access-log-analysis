<?php

require_once('load.php');
require_once('bots.php');

botDoit();
function botDoit() {
	$a = dao_agents::get(); 
	echo('bots:' . "\n\n");
	foreach($a as $r) if (wsal_bots::isBot($r)) echo($r . "\n");
	unset($a);
}




