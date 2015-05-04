<?php
/*
 This is "Query Script which executes bunch of typical data queries. 
 One can run multiple copies of the script to generate concurrency 
*/


/* Device will be passed as first placeholder ,metric as second placeholder, random value between 0 and 1000 3rd one */
$queries=array(
         /* plot information about one metric for 1 device for last hour one data point per minute */
         "MINFO1H" => array(  
           "q"=>'select min(period),count(*),sum(cnt),sum(val) from metrics where device_id=%d and metric_id=%d and period>date_sub(now(), interval 1 hour) group by (unix_timestamp(period) div 60)*60;',
           "num"=>10000,
           "sleep"=>0.02*1000000),
         /* Also an hour but random one for last day */
         "MINFORND1H" => array(  
           "q"=>'select min(period),count(*),sum(cnt),sum(val) from metrics where device_id=%d and metric_id=%d and period between date_sub(now(), interval (%3$d mod 24)+1  hour) and date_sub(now(), interval (%3$d mod 24)  hour)  group by (unix_timestamp(period) div 60)*60;',
           "num"=>3000,
           "sleep"=>0.05*1000000),
         /* Same but re-sample it for 1 day */
         "MINFO1D" => array(  
           "q"=>'select min(period),count(*),sum(cnt),sum(val) from metrics where device_id=%d and metric_id=%d and period>date_sub(now(), interval 1 day) group by (unix_timestamp(period) div 3600)*3600;',
           "num"=>1000,
           "sleep"=>0.1*1000000),
         "TOPDEV1H" => array(  
           "q"=>'select device_id,min(period),count(*),sum(cnt),sum(val) v from metrics where  metric_id=%2$d and period>date_sub(now(), interval 1 hour) group by device_id  order by v desc limit 10;',
           "num"=>1000,
           "sleep"=>0.1*1000000),         
	 "TOPDEV1D" => array(  
           "q"=>'select device_id,min(period),count(*),sum(cnt),sum(val) v from metrics where  metric_id=%2$d and period>date_sub(now(), interval 1 day) group by device_id  order by v desc limit 10;',
           "num"=>100,
           "sleep"=>1*1000000),         
         "TOPM1H" => array(  
           "q"=>'select metric_id,min(period),count(*),sum(cnt),sum(val) v from metrics where  device_id=%d and period>date_sub(now(), interval 1 hour) group by metric_id  order by v desc limit 10;',
           "num"=>50,
           "sleep"=>1*1000000),         
         "TOPM1D" => array(  
           "q"=>'select metric_id,min(period),count(*),sum(cnt),sum(val) v from metrics where  device_id=%d and period>date_sub(now(), interval 1 day) group by metric_id  order by v desc limit 10;',
           "num"=>5,
           "sleep"=>10*1000000),         
         "HOURSUMMARY" => array(
           "q"=>'select min(period),count(*),sum(cnt),sum(val) from metrics where period>date_sub(now(), interval 1 hour) group by (unix_timestamp(period) div 60)*60;',
           "num"=>10,
           "sleep"=>20*1000000),
         "DAYSUMMARY" => array(
           "q"=>'select min(period),count(*),sum(cnt),sum(val) from metrics where period>date_sub(now(), interval 1 day) group by (unix_timestamp(period) div 3600)*3600;',
           "num"=>1,
           "sleep"=>200*1000000),
          );




require 'config.php';


/* Main Program Starts Here */

$mysqli =  mysqli_connect($host,$user,$password,$database);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}



while(true)
{
  $batch_time=0;
  $batch_queries=0;
  /* Go through every query pattern */
  foreach($queries as $k=>$v)
  {
    $num=$v['num'];
    $total_query_time=0;
    for($i=0;$i<$v['num'];$i++)
    {
      $device_id=rand(1,$num_devices);
      $metric_id=rand(1,$num_metrics);
      $rnd=rand(0,1000);
      $q=sprintf($v['q'],$device_id,$metric_id,$rnd);
#      echo($q."\n");
      $query_start_time=microtime(true);
      $res = $mysqli->query($q);  
      if(!$res){
        die('Error : ('. $mysqli->errno .') '. $mysqli->error);
      }
      $res->free(); 
      $query_time=microtime(true)-$query_start_time; 
      $total_query_time+=$query_time;
      /* Instead of specifying the time specified in the configuration sleep as much as query has taken... test */
      /*usleep(rand(0,$v['sleep'])); */
      usleep(1000000*$query_time);
    }
    /* Given Query Done */
    $qps=round($num/$total_query_time,3);
    $batch_time+=$total_query_time;
    $batch_queries+=$num;
    $tqt=round($total_query_time,2);
    echo("$k: $num queries in $tqt seconds;  $qps QPS\n");
  }
  /* Batch Done */
  $tqt=round($batch_time,2);
  $qps=round($batch_queries/$batch_time,3);
  echo("\nBATCH TOTAL  $batch_queries queries in $tqt seconds;  $qps QPS\n\n");
  usleep(round(0,$sleep_between_batches));
} /* While True */

$mysqli->close();

?>

