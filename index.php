<?php

require_once('main.php');
$o = new parse_web_server_access_logs();
$WSALA = $o->get(); unset($o);
require_once('template.php');