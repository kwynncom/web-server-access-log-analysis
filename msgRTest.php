<?php

$mres = msg_get_queue(1, 0600);
$doser = 0;

msg_receive($mres , 1 ,$msgtype,intval(pow(10, 5)),$data,$doser, null,$err);

echo $data;

exit(0);
