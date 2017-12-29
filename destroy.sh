#!/bin/bash
#
# stop and remove all containers and delete data. run this using sudo

docker-compose stop
docker-compose rm -f

rm -rf mariadb_data/*
