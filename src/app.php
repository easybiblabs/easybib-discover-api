<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
            'cookie_lifetime' => 5000,
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
            [
                'title' => 'Grant user scope',
                'desc'  => 'reading and writing users profile data',
                'scope' => 'USER_READ_WRITE',
            ],
            [
                'title' => 'Grant data scope',
                'desc'  => 'reading and writing users projects, citations, comments',
                'scope' => 'DATA_READ_WRITE',
            ],
            [
                'title' => 'Grant user and data scope',
                'desc'  => 'to users profile data, projects, citations and comments',
                'scope' => 'USER_READ_WRITE%20DATA_READ_WRITE',
            ],
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
            'step1.twig',
            [
                'scopes'   => $app['scopes'],
                'authUrl'  => $app['oauth.config']['server.auth'],
                'clientId' => $app['oauth.config']['client.id'],
            ]
        );
    }
)->bind('step1');

$app->get(
    '/step2',
    function () use ($app) {

        return $app['twig']->render(
            'step2.twig',
            [
                'code'     => $app['session']->get('code'),
                'token'    => $app['session']->get('token'),
            ]
        );
    }
)
    ->bind('step2')
    ->before(
        function (Request $request, Application $app) {
            $hasValidToken = function ($token) {
                return isset($token['expires_at']) && $token['expires_at'] > time();
            };
            if ($hasValidToken($app['session']->get('token')) == false) {
                $app['session']->remove('token');
                $app['session']->remove('code');
            }
        }
    );

$app->get(
    '/authorized',
    function (Application $app) {

        if (!$code = $app['request']->get('code')) {
            // the user denied the authorization request
            return $app['twig']->render('denied.twig');
        }

        // request an access_token with the authorization code
        $redirectUri = $app['url_generator']->generate('authorize_redirect', [], true);
        $token = $app['client']->getAccessToken($code, $redirectUri);

        $app['session']->set('token', $token);
        $app['session']->set('code', $code); // this is only for demonstration purpose

        return $app->redirect($app['url_generator']->generate('step2'));
    }
)->bind('authorize_redirect');

$app->get(
    '/discover',
    function (Application $app) {

        $resourceResponse = $app['client']->requestResource(
            $app['session']->get('token'),
            $app['request']->get('url')
        );

        $resourceData = $app['client']->filterHypermediaReferences(
            $resourceResponse['resourceData'],
            $app['url_generator']->generate('discover', ['url' => 'replaceURL'], true)
        );

        return $app['twig']->render(
            'discover.twig',
            [
                'hypermediaResponse' => $resourceData,
                'responseMessage'    => $resourceResponse['responseMessage'],
                'token'              => $app['session']->get('token'),
            ]
        );
    }
)
    ->bind('discover')
    ->before(
        function (Request $request, Application $app) {
            $hasValidToken = function ($token) {
                return isset($token['expires_at']) && $token['expires_at'] > time();
            };
            if ($hasValidToken($app['session']->get('token')) == false) {
                $app['session']->remove('token');
                $app['session']->remove('code');
                return $app->redirect($app['url_generator']->generate('step1'));
            }
        }
    );

$app->get(
    '/readme',
    function () use ($app) {
        return $app['twig']->render(
            'readme.twig',
            [
                'readme'   => file_get_contents(__DIR__ . '/../README.md'),
            ]
        );
    }
)->bind('readme');

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
