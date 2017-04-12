#!/bin/bash
###
# exit on error
###
set -o errexit

###
# Printing functions because it's easy
###
function info () { echo "INFO: ${1}"; }
function error () { echo "ERROR: ${1}"; exit -1; }

###
# Function to install Convox
###
function install_convox {
	info "Validating Convox..."
	if [ ! -n "${THIS_CONVOX_APP}" ] ; then error "Missing '${DEPLOY_TARGET}' var THIS_CONVOX_APP"; fi
	if [ ! -n "${THIS_CONVOX_RACK}" ] ; then error "Missing '${DEPLOY_TARGET}' var THIS_CONVOX_RACK"; fi
	if [ ! -n "${THIS_CONVOX_COMPOSE_FILE}" ] ; then error "Missing '${DEPLOY_TARGET}' var THIS_CONVOX_COMPOSE_FILE"; fi
	if [ ! -n "${THIS_CONVOX_HOST}" ] ; then error "Missing '${DEPLOY_TARGET}' var THIS_CONVOX_HOST"; fi
	if [ ! -n "${THIS_CONVOX_PASSWORD}" ] ; then error "Missing '${DEPLOY_TARGET}' var THIS_CONVOX_PASSWORD"; fi

	## INSTALL convox to /tmp/convox
	info "Downloading and installing convox..."
	curl https://bin.equinox.io/c/jewmwFCp7w9/convox-stable-linux-amd64.tgz -o /tmp/convox.tgz
	tar zxvf /tmp/convox.tgz -C /tmp

	## Make sure convox is runnable
	info "Testing convox..."
	# /tmp/convox -v
}

###
# Get the build target from passed argument
# Default to playground just in case
###
DEPLOY_TARGET=${1:-playground}

###
# Determine our deployment target
# Set any target specific ENV Vars here
###
info "Deploy target set to '${DEPLOY_TARGET}'..."
if [ ${DEPLOY_TARGET} == "production" ] ; then
	THIS_CONVOX_APP=${PRODUCTION_CONVOX_APP}
	THIS_CONVOX_RACK=${PRODUCTION_CONVOX_RACK}
	THIS_CONVOX_COMPOSE_FILE=${PRODUCTION_COMPOSE_FILE:-docker-compose.yml}
	THIS_CONVOX_HOST=${CONVOX_HOST:-console.convox.com}
	THIS_CONVOX_PASSWORD=${CONVOX_PASSWORD}
elif [ ${DEPLOY_TARGET} == "staging" ] ; then
	THIS_CONVOX_APP=${STAGING_CONVOX_APP}
	THIS_CONVOX_RACK=${STAGING_CONVOX_RACK}
	THIS_CONVOX_COMPOSE_FILE=${STAGING_COMPOSE_FILE:-docker-compose.yml}
	THIS_CONVOX_HOST=${CONVOX_HOST:-console.convox.com}
	THIS_CONVOX_PASSWORD=${CONVOX_PASSWORD}
else
	# No matching target found, so error/exit
	error "No matching target for '${DEPLOY_TARGET}' found..."
fi

###
# do any build installations here
###
install_convox

###
# Build the app this could be offloaded to a make file perhaps
###
cd $TRAVIS_BUILD_DIR
if [ ${DEPLOY_TARGET} == "production" ] ; then
	./composer.phar validate
	./composer.phar install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction
	./composer.phar dumpautoload
elif [ ${DEPLOY_TARGET} == "staging" ] ; then
	./composer.phar validate
	./composer.phar install --prefer-dist --ignore-platform-reqs --no-interaction
	./composer.phar dumpautoload
else
	# No matching target found, so error/exit
	error "No matching target for '${DEPLOY_TARGET}' found..."
fi

###
# Do the deployment
###
info "Deploying app '${THIS_CONVOX_APP}' to rack '${THIS_CONVOX_RACK}' with compose.yml file '${THIS_CONVOX_COMPOSE}'..."
/tmp/convox deploy --app ${THIS_CONVOX_APP} --rack ${THIS_CONVOX_RACK} --file ${THIS_CONVOX_COMPOSE}

###
# We are done
###
info "Completed..."
