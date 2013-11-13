<?php

use Silex\Application;

$app = new Application();

$app['appRootPath'] = __DIR__ . '/../';

$app->register(
    new \Silex\Provider\TwigServiceProvider(),
    [
        'twig.path'    => array(__DIR__.'/../templates'),
    ]
);
$app->register(new \Nicl\Silex\MarkdownServiceProvider());

$app['scopes'] = $app->share(
    function () {
        return [
            'USER_READ'       => "read-only access users profile data",
            'USER_READ_WRITE' => "read and write access users profile data",
            'DATA_READ'       => "read-only access to users projects, citations, comments",
            'DATA_READ_WRITE' => "read and write access to users projects, citations, comments",
        ];
    }
);

$app['oauth.config.file'] = $app['appRootPath'] . 'config/oauth.php';
$app['oauth.config'] = $app->share(
    function () use ($app) {
        if (file_exists($app['oauth.config.file'])) {
            return require $app['oauth.config.file'];
        }
        throw new \Exception('Configuration file config/oauth.php is missing.');
    }
);

/** index */
$app->get('/', function () use ($app) {

    return $app['twig']->render(
        'index.twig',
        [
            'scopes'   => $app['scopes'],
            'clientId' => $app['oauth.config']['client.id'],
            'readme'   => file_get_contents(__DIR__. '/../README.md'),
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
