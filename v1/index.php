<?php

require_once('main.php');
// $dao = new dao_wsal();
// $WSALA = $dao->jsget();
// unset($dao);

$o = new parse_web_server_access_logs(0);
$WSALA = $o->getjs();


require_once('out/template.php');