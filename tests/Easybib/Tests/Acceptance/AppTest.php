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

    public function setUp()
    {
        parent::setUp();
        $this->app['session.test'] = true;
        $this->app['oauth.config.file'] = __DIR__ . '/oauthConfig.php';
    }

    public function testAppIsReachable()
    {
        $client = $this->createClient();
        $client->request('GET', '/ping');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('pong', $client->getResponse()->getContent());
    }

    public function testStatusIsOK()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk(), "Status code is ".$client->getResponse()->getStatusCode());
    }

    public function testISeeABeautifulView()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertContains('bootstrap.min.css', $client->getResponse()->getContent());
        $this->assertContains('discover.css', $client->getResponse()->getContent());
    }

    public function testISeeAHeadline()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertContains('Welcome to the EasyBib Demo Client!', $client->getResponse()->getContent());
    }

    public function testTheTheMainPageContainsScopes()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        foreach ($this->app['scopes'] as $scope => $description) {
            $this->assertContains($scope, $client->getResponse()->getContent());
            $this->assertContains($description, $client->getResponse()->getContent());
        }
    }

    public function testAppRootPathIsSet()
    {
        $this->assertNotEmpty($this->app['appRootPath']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Configuration file config/oauth.php is missing.
     */
    public function testOAuthConfigFileIsMissing()
    {
        $this->app['oauth.config.file'] = __DIR__ . '/oauthConfig-notExisting.php';
        $this->app['oauth.config'];
    }

    public function testAuthorizedRedirectActionExists()
    {
        $authorizeRedirectUrl = $this->app['url_generator']->generate('authorize_redirect');
        $client = $this->createClient();
        $client->request('GET', $authorizeRedirectUrl);

        $this->assertNotEquals(
            404,
            $client->getResponse()->getStatusCode(),
            "Action $authorizeRedirectUrl is not defined."
        );
    }

    public function testWeSeePageDeniedIfUserDeniedAccessToClient()
    {
        $authorizeRedirectUrl = $this->app['url_generator']->generate('authorize_redirect');
        $client = $this->createClient();
        $client->request('GET', $authorizeRedirectUrl);

        $this->assertContains('<h3>Authorization Failed!</h3>', $client->getResponse()->getContent());
    }

    public function testWeHaveAHTTPClient()
    {
        $this->app['http.client'];
    }

    public function testRequestingAccessTokenWithAuthorizationCode()
    {
        $this->app['client'] = new \Easybib\Tests\Acceptance\ClientMock();

        $authorizeRedirectUrl = $this->app['url_generator']->generate('authorize_redirect');

        $client = $this->createClient();
        $client->request('GET', $authorizeRedirectUrl, ['code'=>'123']);

        $this->assertNotEmpty($this->app['session']->get('token'));
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
