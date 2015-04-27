<?php
/* The idea of this script is to purge data. 
In theory different devices can have different purge setting this is why we can't use partitioning.  
In this cript for simplicity we do not do that */

require 'config.php';


function generate_purge_device($device_id,$seconds)
{
  $s="DELETE FROM metrics WHERE device_id=$device_id and period<=date_sub(now(), interval $seconds second);";
#  echo("$s\n");
  return $s;
}


/* Main Program Starts Here */

$mysqli =  mysqli_connect($host,$user,$password,$database);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}



$start_time=microtime(true);
while(true)
{
  echo("Starting Purge... ");
  $start_time=microtime(true);
  $rows_deleted=0;
  for($i=0;$i<=$num_devices;$i++)
  {
    $res = $mysqli->query(generate_purge_device($i,$purge_time));  
    if(!$res){
        die('Error : ('. $mysqli->errno .') '. $mysqli->error);
    }
    $rows_deleted+=$mysqli->affected_rows;
  }
  $stop_time=microtime(true);
  $t=$stop_time-$start_time;
  $tx=round($t,3);
  $dps=round($rows_deleted/$t,2);
  echo("$rows_deleted  rows purged  in $tx sec;  $dps  Metrics per second\n");
  usleep(round($purge_sleep*1000000)); /*Sleep in microseconds not seconds */
}

$mysqli->close();

?>

