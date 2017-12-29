#!/bin/bash

source common.sh

now=`date +%Y_%m_%d_%H_%M`

docker exec -it $CONTAINER_DB sh -c "mysqldump -u \$MYSQL_USER -p\$MYSQL_PASSWORD \$MYSQL_DATABASE" | gzip > dump_$now.sql.gz

