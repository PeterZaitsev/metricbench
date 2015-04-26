<?php

# Configuration for benchmark

$period=60;     /* Period to group by  Should be multiple of $load_period */
$load_period=30;   /* Generate all metrics this period of time */

$num_metrics=2000;
$num_devices=200;

$purge_time=48*3600;  /* Time in seconds */
$purge_sleep=300;  /* Sleep between purge rounds */

$sleep_between_batches=300*1000000;  /*beetween batches (us) */


$host='localhost';
$user='root';
$password='';
$database='metrics';
?>
