<?php
namespace Easybib\Tests\Acceptance;

class ClientMock
{
    public function getAccessToken($code)
    {
        return [
            'access_token' => 'aaa',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'scope' => 'USER_READ_WRITE',
            'refresh_token' => 'bbb'
        ];
    }
}
