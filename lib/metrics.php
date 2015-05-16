<?php 

/*Functions to work with metrics data */

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

function generate_purge_device($device_id,$seconds)
{
  $s="DELETE FROM metrics WHERE device_id=$device_id and period<=date_sub(now(), interval $seconds second);";
#  echo("$s\n");
  return $s;
}

?>
