<?php
namespace EasyBib\Service;

class Client
{
    /** @var array */
    private $config;
    /** @var \Guzzle\Http\Client */
    private $httpClient;

    public function __construct(array $config, $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * Get access token from authorization server
     *
     * @param string $code
     * @return array
     */
    public function getAccessToken($code)
    {
        /** @var \Guzzle\Http\Message\Request $requestAccessToken */
        $requestAccessToken = $this->httpClient->post(
            '/oauth/token',
            null,
            [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'client_id' => $this->config['client.id'],
                'client_secret' => $this->config['client.secret'],
                'redirect_uri' => 'http://localhost:9002/authorized',
            ]
        );

        $token = $requestAccessToken->send()->json();

        return $token;
    }
}
