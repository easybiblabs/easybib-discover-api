# EasyBib Silex Skeleton

[![Build Status](https://travis-ci.org/easybiblabs/silex-skeleton.png?branch=master)](https://travis-ci.org/easybiblabs/silex-skeleton)
This silex-skeleton is here to help setting up of new Silex apps for easybib.

* [http://silex.sensiolabs.org/](http://silex.sensiolabs.org/)

## Setup

* Clone this repo `git clone https://github.com/easybiblabs/silex-skeleton.git your-project`
* Go into project-dir: `cd your-project`
* Remove `.git` files with `rm .git`
* Initialize own project repo `git init` and rename everything that belongs to your project
* Run `./composer.phar update` to install dependencies
* Run `./vendor/bin/phpunit` to check that everything works

## Development

Run `php -S localhost:9000 -t www/` and open the app in your browser: [http://localhost:9000/](http://localhost:9000/)

## Continuous Integration (what travis will do before deployment)

All code must be PSR-2 compliant, have no simple checkable code smells and all tests must run successful.

    ./vendor/bin/phpcs --standard=psr2 ./src
    ./vendor/bin/phpmd src/ text codesize,controversial,design,naming,unusedcode
    ./vendor/bin/phpunit


## Deploy


## Everything else
