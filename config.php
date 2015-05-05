<?php

$workload_version="1.03";

require 'local.php';

/* This is advanced configuration */

$period=60;     /* Period to group by  Should be multiple of $load_period */
$load_period=60;   /* Generate all metrics this period of time */

$scale_rt=sqrt($scale);

/* We're looking for devices to be 1000x of metrics. This is not the most realistic but allows us to see how different cardinality indexes behave */

$num_metrics=round(5*$scale_rt);
$num_devices=round(5000*$scale_rt);

$max_metric=1000000;   /*Generate random metrics up N items per batch */
$max_value=1000;   /*Max value generated for metric */



$purge_time=round($length_scale*72*3600);  /* Time in seconds */
$purge_sleep=300;  /* Sleep between purge rounds */

$sleep_between_batches=300*1000000;  /*beetween batches (us) */




?>
