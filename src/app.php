<?php

use Silex\Application;

$app = new Application();

/**
 * Register service provider
 */
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Nicl\Silex\MarkdownServiceProvider());
$app->register(
    new \Guzzle\GuzzleServiceProvider(),
    [
        'guzzle.base_url' => 'https://data.easybib.com',
    ]
);
$app->register(
    new \Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => array(__DIR__ . '/../templates'),
    ]
);
$app->register(
    new \Silex\Provider\SessionServiceProvider(),
    [
        'session.storage.options' => [
            'cookie_lifetime' => 259200000,
        ],
    ]
);

/**
 * Configuration
 */
$app['appRootPath'] = __DIR__ . '/../';
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
$app['http.client'] = $app->share(
    function () use ($app) {
        return $app['guzzle.client'];
    }
);

$app->register(new \EasyBib\Service\ClientServiceProvider());

/**
 * Routes
 */
$app->get(
    '/',
    function () use ($app) {

        return $app['twig']->render(
            'index.twig',
            [
                'scopes'   => $app['scopes'],
                'clientId' => $app['oauth.config']['client.id'],
                'readme'   => file_get_contents(__DIR__ . '/../README.md'),
            ]
        );
    }
)->bind('index');

$app->get(
    '/authorized',
    function (Application $app) {

        if (!$code = $app['request']->get('code')) {
            // the user denied the authorization request
            return $app['twig']->render('denied.twig');
        }

        // request an access_token
        /** @var \EasyBib\Service\Client */
        $client = $app['client'];

        $token = $client->getAccessToken($code);

        $app['session']->set('access_token', $token['access_token']);
        $app['session']->set('refresh_token', $token['refresh_token']);
        $app['session']->set('scope', $token['scope']);


        return $app->redirect($app['url_generator']->generate('index'));
    }
)->bind('authorize_redirect');

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
        throw $e;
        /*
        return $app['twig']->render(
            (404 == $code) ? '404.twig' : '500.twig',
            [
                'code'    => $code,
                'message' => $e->getMessage()
            ]
        );
        */
    }
);

return $app;
