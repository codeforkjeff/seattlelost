#!/bin/bash

cp /opt/citieslost/city_specific/seattle_lost_logo.png /var/www/html/w/resources/assets

php /var/www/html/w/maintenance/pageExists.php Coffee_Messiah

if [ $? -ne 0 ]
then
    echo "Importing from GoSP"
    cd /opt/citieslost/city_specific
    php import_from_ghosts.php
fi

php /var/www/html/w/maintenance/protect.php -u $CL_ADMIN_USERNAME Seattle_Lost

echo "City-specific bootstrap.sh finished"
