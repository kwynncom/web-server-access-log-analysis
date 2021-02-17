<?php

require_once('dao.php');

$dao = new dao_wsal_anal();
$WSALA = $dao->getjs('2015-10-15');


require_once(__DIR__ . '/' . 'out/template.php');
