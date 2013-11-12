<?php

use Silex\Application;

$app = new Application();

$app->register(
    new \Silex\Provider\TwigServiceProvider(),
    [
        'twig.path'    => array(__DIR__.'/../templates'),
    ]
);

$app['scopes'] = $app->share(
    function () {
        return [
            'USER_READ'         => "read-only access users profile data",
            'USER_READ_WRITE'   => "read and write access users profile data",
            'DATA_READ'         => "read-only access to users projects, citations, comments",
            'DATA_READ_WRITE'   => "read and write access to users projects, citations, comments",
        ];
    }
);

/** index */
$app->get('/', function () use ($app) {

    return $app['twig']->render(
        'index.twig',
        [
            'scopes' => $app['scopes'],
        ]
    );
})->bind('index');

/** Is page reachable */
$app->get(
    '/ping',
    function () use ($app) {
        return $app->json(array("status" => "ok", "data" => "pong"));
    }
)->bind('ping');

/** Error handler */
$app->error(
    function (\Exception $e, $code) use ($app) {
        return $app['twig']->render(
            (404 == $code) ? '404.twig' : '500.twig',
            [
                'code' => $code,
                'message' => $e->getMessage()
            ]
        );
    }
);


return $app;
