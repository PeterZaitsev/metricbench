<?php
/*
  Just compute some numbers based on config 
*/

$row_length=100;  /*Assume this row size */


require 'config.php';


/* Main Program Starts Here */

$total_metrics=$num_metrics*$num_devices;

$wps_needed=$total_metrics/$load_period;

$data_inflow=$total_metrics/$period;

$rows_to_keep=$data_inflow*$purge_time;

echo("Running workload version $workload_version\n");
echo("Storing $total_metrics metrics from  $num_devices devices  ($num_metrics per device)\n");
printf("This will require system handling %d writes per second;  %d/sec new rows added to the database\n",$wps_needed,$data_inflow);
printf("%.2fM rows will be kept in the database for retention period of %f hours\n",$rows_to_keep/1000000,$purge_time/3600);
printf("This is %.2fM rows per hour;  %.2fM rows per day\n",$data_inflow*3600/1000000,$data_inflow*86400/1000000);
printf("With logical row_size of $row_length it should take %0.2fGB of logical database size\n",$rows_to_keep*$row_length/(1024*1024*1024));


?>

