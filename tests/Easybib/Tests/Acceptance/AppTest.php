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
        $client->request('GET', '/ping');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('pong', $client->getResponse()->getContent());
    }

    public function testISeeABeautifulView()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('bootstrap_2_2_2.min.css', $client->getResponse()->getContent());
        $this->assertContains('discover.css', $client->getResponse()->getContent());
    }

    public function testISeeAHeadline()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('<h3>Welcome to the EasyBib Discover Client!</h3>', $client->getResponse()->getContent());
    }

    public function testISeeAListOnTheMainPage()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('<ul>', $client->getResponse()->getContent());
    }

    public function testTheListOnTheMainPageContainsTheScopes()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        foreach ($this->app['scopes'] as $scope => $description) {
            $this->assertContains($scope, $client->getResponse()->getContent());
            $this->assertContains($description, $client->getResponse()->getContent());
        }
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
