<?php

$semid = sem_get(1, 1, 0600, 1);
$ar = sem_acquire($semid, true);
if ($ar) sleep(5);
else die('could not get sem lock');
if ($ar) sem_release($semid);
