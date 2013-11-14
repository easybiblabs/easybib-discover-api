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

    /**
     * @param string $encodedUrl
     * @param array $token
     *
     * @return array
     */
    public function requestResource($encodedUrl, array $token)
    {
        $url = urldecode($encodedUrl);

        $headers = [
            'Authorization' => sprintf('Bearer %s', $token['access_token']),
            'Accept'        => 'application/vnd.com.easybib.data+json',
        ];

        ob_start();
        $request = $this->httpClient->get($url, $headers, ['debug' => true]);
        $data = $request->send();
        $responseMessage = ob_get_contents();
        ob_end_clean();

        return [
            'resourceData'    => $data->json(),
            'responseMessage' => $responseMessage
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
                    '<a href=%s data-id=next>%s</a>',
                    str_replace('replaceURL', urlencode($resourceData['href']), $urlTemplate),
                    $resourceData['href']
                );
            } else {
                $resourceData[$key] = $this->filterHypermediaReferences($resourceData[$key], $urlTemplate);
            }
        }
        return $resourceData;
    }
}
