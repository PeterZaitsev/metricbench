<?php
/* The idea of this script is as follows. We have N devices which generate bunch of metrics every $load_period
We try to go ahead and load all metrics for all devices every seconds. If we fail we generate the warning */

require 'config.php';

function get_metric_data() /* unused */
{
  global $num_metrics;
  global $num_devices;
  global $period;

  $r=array();
  $r['ts']= ((int)(time()/$period))*$period;
  $r['device_id']=rand(1,$num_devices);
  $r['metric_id']=rand(1,$num_metrics);
  $r['val']=rand(0,100);

  return $r; 
}

function generate_multi_insert($device_id)
{
  global $period;
  global $num_metrics;

  $ts=((int)(time()/$period))*$period;
  $val=rand(0,100);
  $s="INSERT INTO metrics (period,device_id,metric_id,cnt,val) values ";
  for($i=1;$i<=$num_metrics;$i++)
  {
    $val=rand(0,100);
    $s=$s."(from_unixtime($ts),$device_id,$i,1,$val),";
  }
  $s=rtrim($s,',');   
  $s=$s." on duplicate key update cnt=cnt+1,val=val+values(val);";
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
  $start_time=microtime(true);
  for($i=0;$i<=$num_devices;$i++)
  {  
    $insert_row = $mysqli->query(generate_multi_insert($i));  
    if(!$insert_row){
        die('Error : ('. $mysqli->errno .') '. $mysqli->error);
    }
  }
  $stop_time=microtime(true);
  $t=$stop_time-$start_time;
  $tx=round($t,3);
  $total=$num_devices*$num_metrics;
  $mps=round($total/$t);
  echo("$num_devices  DEVICES  $total METRICS in $tx sec;  $mps  Metrics per second\n");
  if ($t>$load_period)   /* Want to be able to do it once per second all the time */
    echo("WARNING: UNABLE TO KEEP UP!!! \n");
  $x=$load_period-(microtime(true)-$start_time);  /*Get it again */
  if ($x>0)
   usleep(round($x*1000000)); /*Sleep in microseconds not seconds */
}

$mysqli->close();

?>

