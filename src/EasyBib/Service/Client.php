<?php
namespace EasyBib\Service;

class Client
{
    public static $CLASS = __CLASS__;

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
    public function getAccessToken($code, $redirectUri)
    {
        /** @var \Guzzle\Http\Message\Request $requestAccessToken */
        $requestAccessToken = $this->httpClient->post(
            $this->config['server.token'],
            null,
            [
                'code' => $code,
                'grant_type'    => 'authorization_code',
                'client_id'     => $this->config['client.id'],
                'client_secret' => $this->config['client.secret'],
                'redirect_uri'  => $redirectUri,
            ]
        );

        $token = $requestAccessToken->send()->json();

        $token = $this->setTokenExpiresAt($token);

        return $token;
    }

    /**
     * @param string $encodedUrl
     * @param array $token
     *
     * @return array
     */
    public function requestResource(array $token, $encodedUrl = null)
    {
        $headers = [
            'Authorization' => sprintf('Bearer %s', $token['access_token']),
            'Accept'        => 'application/vnd.com.easybib.data+json',
        ];

        $url = $this->getResourceUrl($token['scope'], $encodedUrl);

        ob_start();
        $request = $this->httpClient->get($url, $headers, ['debug' => true]);
        $data = $request->send();
        $responseMessage = ob_get_contents();
        ob_end_clean();

        return [
            'resourceData'    => $data->json(),
            'responseMessage' => $responseMessage,
            'url' => $url,
        ];
    }

    /**
     * Replaces all href values with links to this app
     *
     * @param array  $resourceData
     * @param string $urlTemplate
     *
     * @return array
     */
    public function filterHypermediaReferences($resourceData, $urlTemplate)
    {
        if (!is_array($resourceData)) {
            return $resourceData;
        }
        foreach (array_keys($resourceData) as $key) {
            if ($key === 'href') {
                $resourceData['href'] = sprintf(
                    '<a href="%s" data-id="next">%s</a>',
                    str_replace('replaceURL', urlencode($resourceData['href']), $urlTemplate),
                    $resourceData['href']
                );
                continue;
            }

            $resourceData[$key] = $this->filterHypermediaReferences($resourceData[$key], $urlTemplate);

        }
        return $resourceData;
    }

    /**
     * @param string $scope
     * @param string $encodedUrl
     */
    private function getResourceUrl($scope, $encodedUrl = null)
    {
        if ($encodedUrl != null) {
            return urldecode($encodedUrl);
        }
        if (stripos($scope, 'data') !== false) {
            return urldecode($this->config['entrypoint.data']);
        }
        return urldecode($this->config['entrypoint.user']);
    }

    /**
     * @param $token
     *
     * @return mixed
     */
    private function setTokenExpiresAt($token)
    {
        $token['expires_at'] = $token['expires_in'] + time();
        return $token;
    }
}
