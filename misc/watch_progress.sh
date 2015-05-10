watch " mysql -t -e \" select event,pid,time(ts) tm, name, num_queries, num_rows, round(time_total,2) total, round(time_max*1000,2) max_ms,  round(num_queries/time_total,2) qps, round(num_rows/time_total,2) rps, status   from metrics.log order by id desc limit 40;\""
 
