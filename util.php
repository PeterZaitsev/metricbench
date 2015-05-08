<?php

/* Globally usable functions */


/* The query function which should be bullet proof */
function very_safe_query($query)  
{
  global $mysqli;
  global $host;
  global $user;
  global $password;
  global $database;

  $need_reconnect=false;
  
  reconnect:
  while (!$mysqli || $need_reconnect) /* If we're not connected */
  {     
    $mysqli =  @mysqli_connect($host,$user,$password,$database);
      if (mysqli_connect_error()) 
      {
        error_log('Error Connecting to MySQL: ('. mysqli_connect_errno() .') '. mysqli_connect_error());
        sleep(1); /* Sleep 1 sec and retry */ 
      }
      else
        $need_reconnect=false;
   }
   /* Now we're connected Do query until success*/
   do
   {
     $res = $mysqli->query($query);
     if(!$res)
     {
        error_log('Error Executing Query: ('. $mysqli->errno .') '. $mysqli->error);
        if(!$mysqli->ping())   /* Try to reconnect */
        {
          $need_reconnect=true;
          goto reconnect;
        }
     }
   } while(!$res);
   /* At this point we always should have query done */        
   return $res;
}

/* Store progress report into log table */

function log_progress($name,$num_queries,$num_rows,$time_total,$time_max,$status)
{
  $q="INSERT INTO log (ts,name,num_queries,num_rows,time_total,time_max,status) VALUES (now(),'$name',$num_queries,$num_rows,$time_total,$time_max,'$status');";
#  echo($q);
  very_safe_query($q);
}




/* Test  */

/*

require_once 'config.php';

#$user='aaa';

#log_progress('test_progress',10,1000,1.0,1.1,'OK');


while(true)
 very_safe_query("select sleep(10)");

*/

?>
