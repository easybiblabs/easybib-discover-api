#!/bin/sh

# Build the configuration required from ENV vars post boot - pre start
php /webroot/docker/build_config.php

# if [ "$APP_ENVIRONMENT" == "DEVELOPMENT" ] ; then
	# This is here for debug purposes and is not required in production
	# ./composer.phar install
# fi
