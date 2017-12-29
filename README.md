
# Cities Lost: Seattle

Repo of code for running the citieslost platform for Seattle. This is
experimental right now.

This is useful as a starting point for your own city.

## Installation

Clone the [citieslost](https://github.com/codeforkjeff/citieslost)
repository and build the docker image.

Make copies of the following files, removing the .sample extension, and fill in the values in each:

```
# environment variables, used by both wiki and db containers
cp environment.sample environment
# city-specific config overrides for mediawiki
cp citieslost_city_specific.php.sample citieslost_city_specific.php
# configuration options for the map application
cp map/config.json.sample map/config.json
```

Run `docker-compose up -d`

The container bootstrap process can take ~30s before the web server
begins servicing requests. The very first time it's run, it will take
a little under 10 minutes to start, to import data from
[Ghosts of Seattle Past](http://www.seattleghosts.com). You can run
`docker logs -f seattlelost_wiki_1` to see what's happening.

## Updating

If you update the citieslost image, you can destroy and restart only
the wiki container by running:

```
docker-compose stop wiki
docker-compose rm -f wiki
docker-compose up -d
```

This should preserve your data.

## Deploying

The docker-compose file is set up to run the application on
port 8080. You can put a proxy in front of it, or change it to port
80.

## TODO

- import photos from GoSP
