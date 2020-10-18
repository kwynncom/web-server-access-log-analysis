<?php

require_once('main.php');
$dao = new dao_wsal();
$WSALA = $dao->jsget();
unset($dao);
require_once('out/template.php');