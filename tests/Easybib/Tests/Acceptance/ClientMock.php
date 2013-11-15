<?php
namespace Easybib\Tests\Acceptance;

use EasyBib\Service\Client;

class ClientMock extends Client
{
    public function __construct()
    {
    }

    public function getAccessToken($code, $redirectUri)
    {
        return [
            'access_token' => 'aaa',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'scope' => 'USER_READ_WRITE DATA_READ_WRITE',
            'refresh_token' => 'bbb'
        ];
    }

    public function requestResource(array $token, $encodedUrl = null)
    {
        return [
            'resourceData'    => [],
            'responseMessage' => 'response message'
        ];
    }
}
