<?php

$workload_version="1.02";

# Configuration for benchmark

$scale=16;
$length_scale=1;   /* Scale length of data store. Store 3 days typically */

/* This is advanced configuration */

$period=60;     /* Period to group by  Should be multiple of $load_period */
$load_period=60;   /* Generate all metrics this period of time */

$scale_rt=sqrt($scale);

/* We're looking for metrics to be 100x of number of devices. This is not realistic but it is a good test for handling different cardinality in indexes */

$num_metrics=round(1000*$scale_rt);
$num_devices=round(10*$scale_rt);

$max_metric=1000000;   /*Generate random metrics up N items per batch */
$max_value=1000;   /*Max value generated for metric */



$purge_time=round($length_scale*72*3600);  /* Time in seconds */
$purge_sleep=300;  /* Sleep between purge rounds */

$sleep_between_batches=300*1000000;  /*beetween batches (us) */


$host='localhost';
$user='root';
$password='';
$database='metrics';
?>
