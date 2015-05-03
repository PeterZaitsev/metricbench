# Script to start all processes should be run on screen 
php compute.php
php gen_metrics.php >> ./log/gen_metrics.log &
php purge.php >> ./log/purge.log &
php query.php >> ./log/query1.log &
sleep 600
php query.php >> ./log/query2.log &
echo "All Started!"

