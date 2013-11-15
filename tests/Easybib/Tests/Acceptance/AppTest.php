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
        $this->app['session']->remove('token');
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

        foreach ($this->app['scopes'] as $scope) {
            $this->assertContains($scope['title'], $client->getResponse()->getContent());
            $this->assertContains($scope['desc'], $client->getResponse()->getContent());
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

    public function testClientIsInstanceExists()
    {
        $this->assertInstanceOf(\EasyBib\Service\Client::$CLASS, $this->app['client']);
    }

    public function testRequestingAccessTokenWithAuthorizationCode()
    {
        $this->app['client'] = new \Easybib\Tests\Acceptance\ClientMock();

        $authorizeRedirectUrl = $this->app['url_generator']->generate('authorize_redirect');

        $client = $this->createClient();
        $client->request('GET', $authorizeRedirectUrl, ['code'=>'123']);

        $this->assertNotEmpty($this->app['session']->get('token'));
    }

    public function testRedirectIfTokenIsExpired()
    {
        $this->app['session']->set('token', ['expires_at' => time()-10]);

        $discoverUrl = $this->app['url_generator']->generate('discover');

        $client = $this->createClient();
        $client->request('GET', $discoverUrl);

        $this->assertContains('Redirecting to /', $client->getResponse()->getContent());
    }

    public function testDiscoverPageShowsAccessToken()
    {
        $this->app['session']->set('token', ['expires_at' => time()+10, 'access_token' => 'aaa']);
        $this->app['client'] = new \Easybib\Tests\Acceptance\ClientMock();

        $discoverUrl = $this->app['url_generator']->generate('discover');

        $client = $this->createClient();
        $client->request('GET', $discoverUrl, ['rel' => 'project']);

        $this->assertContains('aaa', $client->getResponse()->getContent());
    }

    public function testClientRequestResource()
    {
        $this->app['client'] = new \Easybib\Tests\Acceptance\ClientMock();
        $this->app['client']->requestResource(['access_token' => 'tokenABC'], 'http://localhost');
    }


    public function testReplaceLinks()
    {
        $data = [
            'data' => [
                [
                    'links' => [
                        [
                            'href' => 'url1'
                        ]
                    ],
                    'data' => [],
                ]
            ],
            'links' => [
                [
                    'href' => 'url2'
                ]
            ],
        ];
        $result = $this->app['client']->filterHypermediaReferences($data, 'replaceURL');

        $this->assertEquals('<a href=url1 data-id=next>url1</a>', $result['data'][0]['links'][0]['href']);
        $this->assertEquals('<a href=url2 data-id=next>url2</a>', $result['links'][0]['href']);
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
