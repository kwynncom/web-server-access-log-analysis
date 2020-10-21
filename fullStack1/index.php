<?php

require_once('dao.php');

$dao = new dao_wsal_anal();
$WSALA = $dao->getjs('2020-10-15');


require_once('out/template.php');
