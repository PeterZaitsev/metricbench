<?php

/* This is the test for highly compressable message logging 

Devices report something like status or error messages;

Devices report error messages for "objects" for example lines of code in the file or modules, which are also stored

*/

$max_error_id=120;


/*
for($i=0; $i<$max_error_id; $i++)
{
 echo $i.' : '.posix_strerror($i)."\n";
}
*/


require '../conf/stress_config.php';
require '../lib/util.php';
require '../lib/metrics.php';

$total_loaders=1;
$current_loader=1;

/* Optionally run with parameters if we're doing it in parallel*/
if ($argc==3)
{
  $total_loaders=$argv[1];
  $current_loader=$argv[2];
}


echo("Started loader $current_loader out of $total_loaders\n");



$id=$current_loader; /* Start with this value */
while(true)
{
  $code=rand(0,$max_error_id);
  $val=rand(1,10000); /*Random message modifier*/ 
  $device_id=rand(1,1000000000);  /* Use different here ? */
  $object='MAIN';
  $msg=posix_strerror($code)." for $device_id at $object:$val";
  $q="INSERT INTO messages (id,ts,device_id,object,error_code,message) values ($id,now(),$device_id,'$object',$code,'$msg');";
  #echo "$q\n";;
  very_safe_query($q);
  $id+=$total_loaders;
  if ($id>1000000000)   /* Assume no more than 1bil inserts per second */
    $id=$current_loader;
}

exit;



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

