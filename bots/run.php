<?php

require_once('load.php');
require_once('bots.php');

botDoit();
function botDoit() {
	$aa = dao_agents::get(); 
	$i = 0;
	$gri = 0;
	foreach($aa as $ra) if ((wsal_bots::botPercentage($ra['_id']) >= 80)) {
		echo(++$i . ' ' . $ra['count'] . ' ' . $ra['_id'] . "\n");
		$gri += $ra['count'];
	}
	unset($a);
	echo('GRAND TOTAL: ' . $gri . "\n");
}




