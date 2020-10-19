<?php

$mres = msg_get_queue(1, 0600);
$doser = 0;

msg_receive($mres , 1, $msgtype, 200, $data, 1, null,$err);

echo $data;

exit(0);
