#!/bin/sh

# Build the configuration required from ENV vars post boot - pre start
php /webroot/build_config.php

# if [ "$APP_ENVIRONMENT" == "DEV" ] ; then
	# This is here for debug purposes and is not required in production
	# ./composer.phar install
# fi
