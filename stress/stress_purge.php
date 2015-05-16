<?php

/*  Purge data as quickly as possible

Multple Purgers can be started at once to see concurrent load in this case we should run it as 

stress_purge.php  total_purgers  current_purgers  

to indicate total_loaders are running and this one is current_loader of them

*/

require '../conf/stress_config.php';
require '../lib/util.php';
require '../lib/metrics.php';

$total_scripts=1;
$current_script=1;

/* Optionally run with parameters */
if ($argc==3)
{
  $total_scripts=$argv[1];
  $current_script=$argv[2];
}

$loader_devices=round($num_devices/$total_scripts);
$start_device=$loader_devices*($current_script-1)+1;
$end_device=$start_device+$loader_devices-1;


echo("Started purger $current_script out of $total_scripts handling devices from $start_device to $end_device\n");


$start_time=microtime(true);
while(true)
{
  log_progress('BEGIN','STRESS_PURGE',0,0,0,0,'');
  $start_time=microtime(true);
  $rows_deleted=0;
  $max_time=0;
  for($i=$start_device;$i<=$end_device;$i++)
  {
    $st=microtime(true);
    $res = very_safe_query(generate_purge_device($i,$purge_time));
    $l=microtime(true)-$st;
    if ($l>$max_time)
      $max_time=$l;
    $rows_deleted+=$mysqli->affected_rows;
  }
  $stop_time=microtime(true);
  $t=$stop_time-$start_time;
  log_progress('END','STRESS_PURGE',$loader_devices,$rows_deleted,$t,$max_time,'OK');
  $tx=round($t,3);
  $dps=round($rows_deleted/$t,2);
  echo("[$current_script] $rows_deleted  rows purged  in $tx sec;  $dps  Metrics per second\n");
  if ($rows_deleted==0)
    usleep(10*1000000); /* In unlikely case there is nothing to do sleep 10 sec to avoid resource waste */
}

$mysqli->close();


?>

