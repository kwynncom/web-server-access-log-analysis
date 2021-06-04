<?php

require_once('datToWeb.php');
header('Content-Type: application/json');
echo(agent_to_web::getJSON());
