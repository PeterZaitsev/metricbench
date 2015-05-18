<?php

/* Stress load data as quickly as possible   

Multple loaders can be started at once to see concurrent load in this case we should run it as 

stress_load.php  total_loaders  current_loader  

to indicate total_loaders are running and this one is current_loader of them

*/

require '../conf/stress_config.php';
require '../lib/util.php';
require '../lib/metrics.php';

$total_loaders=1;
$current_loader=1;

/* Optionally run with parameters */
if ($argc==3)
{
  $total_loaders=$argv[1];
  $current_loader=$argv[2];
}

$loader_devices=round($num_devices/$total_loaders);
$start_device=$loader_devices*($current_loader-1)+1;
$end_device=$start_device+$loader_devices-1;


echo("Started loader $current_loader out of $total_loaders handling devices from $start_device to $end_device\n");


/* We want to handle devices in the "random" order to illustrate case as they could come to the queue in diferent order*/

$devices=range($start_device,$end_device);

$start_time=microtime(true);
while(true)
{
  shuffle($devices);
  log_progress('BEGIN','STRESS_LOAD',0,0,0,0,'');
  $start_time=microtime(true);
  $max_time=0;
  foreach($devices as $i)
  {  
    $st=microtime(true);
    $insert_row = very_safe_query(generate_multi_insert($i));  
    $l=microtime(true)-$st;
    if ($l>$max_time)
     $max_time=$l;
  }
  $stop_time=microtime(true);
  $t=$stop_time-$start_time;
  $tx=round($t,3);
  $total=$loader_devices*$num_metrics;
  $mps=round($total/$t);
  $status='OK';
  echo("[$current_loader] $loader_devices  DEVICES  $total METRICS in $tx sec;  $mps  Metrics per second\n");
  log_progress('END','STRESS_LOAD',$loader_devices,$loader_devices*$num_metrics,$t,$max_time,$status);
}

$mysqli->close();

?>

