### Discover EasyBibs Api

[![Build Status](https://travis-ci.org/easybiblabs/easybib-discover-api.png?branch=master)](https://travis-ci.org/easybiblabs/easybib-discover-api)

We would like to guide you through the creation of an OAuth2 client for requesting data from EasyBib's API. This client is opensource under MIT license and can be setup by following the description below.

You can also try it by [Going directly to https://discover.data.easybib.com/](https://discover.data.easybib.com/) or, if you have not already started [creating an EasyBib API client here](https://data.easybib.com/)

### Register a client application

* You need to be registered and logged in at [www.easybib.com/](http://www.easybib.com/)
* Go to [data.easybib.com](https://data.easybib.com/)
* Click on `Manage Clients` to go get to see all of your registered client applications
* Click on `Add an application` button to get to the form
* Fill out required fields:
  * Application Name: `My Client App`
  * Redirect Uri: `http://localhost:9000/authorized`

### Setup this client

* Clone this repo `git clone https://github.com/easybiblabs/easybib-discover-api.git discover`
* Go into project-dir: `cd discover`
* Run `./composer.phar update` to install dependencies
* Create config and add your client credentials `cp config/oauth.php.dist config/oauth.php`
* Run `./phpci` to check that everything works
* Run `php -S localhost:9000 -t www/` and open the app in your browser: [http://localhost:9000/](http://localhost:9000/)

### Routes

* [/](/) Step 1
* [/step2](/step2) Step 2
* [/authorized](/authorized) Redirect Uri
* [/discover](/discover) Discover the API
* [/reset](/reset) Remove token from session
* [/readme](/readme) This readme



### Helpful links

This client application was developed with Silex: [http://silex.sensiolabs.org/](http://silex.sensiolabs.org/)
