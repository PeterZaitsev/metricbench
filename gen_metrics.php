<?php
/* The idea of this script is as follows. We have N devices which generate bunch of metrics every $load_period
We try to go ahead and load all metrics for all devices every seconds. If we fail we generate the warning */

require 'config.php';
require 'util.php';

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
  global $max_metric;
  global $max_value;

#  $num_metrics=2;  /*Debug */

/* We assume $num_metrics come in the batch  */

  $ts=((int)(time()/$period))*$period;
  $val=rand(0,100);
  $s="INSERT INTO metrics (period,device_id,metric_id,cnt,val) values ";
  $sql=array();
  for($i=1;$i<=$num_metrics;$i++)
  {
    $val=rand(0,$max_value);
    $sql[]="(from_unixtime($ts),$device_id,$i,1,$val)";
  }
  /* Generate statement from associative array in one go */
  $s=$s.implode(',',$sql)." on duplicate key update cnt=cnt+1,val=val+values(val);";
#  echo("$s\n");
  return $s;
}


/* Main Program Starts Here */

/* We want to handle devices in the "random" order to illustrate case as they could come to the queue in diferent order*/

$devices=range(1,$num_devices);

$start_time=microtime(true);
while(true)
{
  shuffle($devices);
  log_progress('BEGIN','GEN_DATA',0,0,0,0,'');
  $start_time=microtime(true);
  $max_time=0;
  /* Instead of doing for loop we're iterating through shufled array of devices */
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
  $total=$num_devices*$num_metrics;
  $mps=round($total/$t);
  $status='OK';
  echo("$num_devices  DEVICES  $total METRICS in $tx sec;  $mps  Metrics per second\n");
  if ($t>$load_period)   /* Want to be able to do it once per second all the time */
  {
    echo("WARNING: UNABLE TO KEEP UP!!! \n");
    $status='UNABLE TO KEEP UP';
  }
  log_progress('END','GEN_DATA',$num_devices,$num_devices*$num_metrics,$t,$max_time,$status);
  $x=$load_period-(microtime(true)-$start_time);  /*Get it again */
  if ($x>0)
   usleep(round($x*1000000)); /*Sleep in microseconds not seconds */
}

$mysqli->close();

?>

