<?php

$workload_version="1.04";

require 'local.php';

/* This is advanced configuration */

$period=60;     /* Period to group by  Should be multiple of $load_period */
$load_period=60;   /* Generate all metrics this period of time */

/* We're scaling number of devices; Keeping 10 same number of metrics per device */


$num_metrics=round(10);
$num_devices=round(1000*$scale);

$max_metric=1000000;   /*Generate random metrics up N items per batch */
$max_value=1000;   /*Max value generated for metric */



$purge_time=round($length_scale*72*3600);  /* Time in seconds */
$purge_sleep=300;  /* Sleep between purge rounds */

$sleep_between_batches=300*1000000;  /*beetween batches (us) */




?>
