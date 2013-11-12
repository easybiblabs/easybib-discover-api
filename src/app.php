<?php

use Silex\Application;

$app = new Application();

$app->get('/', function () use ($app) {
        return 'Discover EasyBibs Api';
});


return $app;
