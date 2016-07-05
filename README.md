### Discover EasyBib's Api

[![Build Status](https://travis-ci.org/easybiblabs/easybib-discover-api.png?branch=master)](https://travis-ci.org/easybiblabs/easybib-discover-api)

**"easybib-discover-api"** walks you through the setup of an OAuth2 client to request data from EasyBib's API. This client is opensource under Apache 2 license and can be setup by following the description below.

You can also try it by going directly to [https://discover.data.easybib.com/](https://discover.data.easybib.com/) or, if you have not already started [creating an EasyBib API client here](https://data.easybib.com/)

### Register a client application

* You need to be registered and logged in at [http://www.easybib.com](http://www.easybib.com/)
* Go to [https://data.easybib.com](https://data.easybib.com/)
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
* Set environment variables
* Run `./phpci` to check that everything works
* Run `php -S localhost:9000 -t www/` and open the app in your browser: [http://localhost:9000/](http://localhost:9000/)

#### Example oauth.php variables

    putenv('OAUTH_URL_ID=https://id.easybib.com');
    putenv('OAUTH_URL_DATA=https://data.easybib.com');
    putenv('OAUTH_ID=local-vagrant-api-id');
    putenv('OAUTH_SECRET=local-vagrant-api-secret');


### Routes

* [/](/) Step 1
* [/step2](/step2) Step 2
* [/authorized](/authorized) Redirect Uri
* [/discover](/discover) Discover the API
* [/reset](/reset) Remove token from session
* [/readme](/readme) This readme



### Helpful links

This client application was developed with Silex: [http://silex.sensiolabs.org/](http://silex.sensiolabs.org/)
