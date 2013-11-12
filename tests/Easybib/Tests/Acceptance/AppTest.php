<?php
namespace Easybib\Tests\Acceptance;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * Class AppTest
 *
 * @see http://silex.sensiolabs.org/doc/testing.html
 */
class AppTest extends WebTestCase
{

    public function testAppIsReachable()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('Discover EasyBibs Api', $client->getResponse()->getContent());

    }

    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap.php';
    }
}
