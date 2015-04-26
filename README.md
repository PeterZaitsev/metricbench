# metricbench

The goal of this benchmark is to look at the database behavior in the metrics capture and processing use case
There is defined number of devices generating N metrics each which need to be ingested in the database and stored for period of time.

Depending on benchmark configuration you can make it either only to load the new data or update data which has been last loaded through INSERT ON DUPLICATE KEY UPDATE

This benchmark focuses on rather unoptimized schema and process. In real life one might use Redis or other caching to avoid updating database frequently as well as potentially use partitioning etc.  We want to see the behavior with most "naive" implementation to see where it takes us as would not we rather see database being able to handle workloads without us needed to resort to advanced optimizations ?

Unlike many benchmarks which have just one pattern going all the time this benchmark have several processes which have different behavior to get us close to real life systems which have multiple of process going on at different times of day etc.

get_metrics.php  process  generates metrics at the desired pace.   You need to make sure it is able to keepup
purge.php - purges too old metrics
query.php - runs queries of different types (running from ranging from simple to complicated. see code)

The best way to run the benchmark is to run  start_all.sh  which will start  data load process; purge process and 2 query processes.  Adjust script if you want more query concurrency.

see .log files to get the performance numbers or sign up for Percona cloud tools at http://cloud.percona.com to get moe details about different query response times and database load.

This is the "long benchmark"  it is expected to be run over the cause of several days (or even weeks) to show how system can behave over time and to ensure the required amount of data is generated.  As you start it first it will start producing results but they will not be relevant until needed amount of rows will be generated and purge starts to purging some.

## Preparing data

see config.php to define how much data you need and database configuration.  

create the table definition by doing   mysql metrics < create.tokudb.sql    before starting run.

