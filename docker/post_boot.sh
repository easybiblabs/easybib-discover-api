#!/bin/sh

php /webroot/build_config.php

if [ "$APP_ENVIRONMENT" == "DEV" ] ; then
	./composer.phar install --dev --prefer-source
fi
