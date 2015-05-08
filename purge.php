<?php
/* The idea of this script is to purge data. 
In theory different devices can have different purge setting this is why we can't use partitioning.  
In this cript for simplicity we do not do that */

require 'config.php';
require 'util.php';


function generate_purge_device($device_id,$seconds)
{
  $s="DELETE FROM metrics WHERE device_id=$device_id and period<=date_sub(now(), interval $seconds second);";
#  echo("$s\n");
  return $s;
}


/* Main Program Starts Here */


$start_time=microtime(true);
while(true)
{
  echo("Starting Purge... ");
  $start_time=microtime(true);
  $rows_deleted=0;
  $max_time=0;
  for($i=0;$i<=$num_devices;$i++)
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
  log_progress('PURGE',$num_devices,$rows_deleted,$t,$max_time,'OK');
  $tx=round($t,3);
  $dps=round($rows_deleted/$t,2);
  echo("$rows_deleted  rows purged  in $tx sec;  $dps  Metrics per second\n");
  usleep(round($purge_sleep*1000000)); /*Sleep in microseconds not seconds */
}

$mysqli->close();

?>

