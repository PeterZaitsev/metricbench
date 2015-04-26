# Script to start all processes should be run on screen 
php compute.php
php gen_metrics.php > gen_metrics.log &
php purge.php > purge.log &
php query.php > query1.log &
sleep 60
php query.php > query2.log &
echo "All Started!"

