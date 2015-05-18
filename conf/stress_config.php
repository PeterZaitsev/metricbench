<?php

/* For stress config we're using different config which is designed for very agressive insertion */


require 'stress_local.php';

/* This is advanced configuration */

$period=1;     /* Period to group by  Should be multiple of $load_period */
$load_period=1;   /* Generate all metrics this period of time */

$random_metrics=1;   /*Generate random metrics instead of sequential */
$max_metric_value=1000000; /*Metrics in this range */


/* We're scaling number of devices; Keeping 10 same number of metrics per device */


$num_metrics=round(10);
$num_devices=round(1000*$scale);

$max_value=1000;   /*Max value generated for metric */


$purge_time=round($length_scale*3*3600);  /* Time in seconds Default 3h worth of data for stress */


?>
