<?php
namespace EasyBib\Service;

use Silex\Application;

class ClientServiceProvider implements \Silex\ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['client'] = $app->share(
            function () use ($app) {
                return new Client($app['oauth.config'], $app['http.client'], $app['logger']);
            }
        );
    }

    /**
     * @param Application $app
     * @SuppressWarnings("unused")
     */
    public function boot(Application $app)
    {
    }
}
