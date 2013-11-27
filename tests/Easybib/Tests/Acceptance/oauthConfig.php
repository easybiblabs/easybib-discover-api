<?php

return [
    'server.auth'     => getenv('DISCOVER_API_OAUTH_URL') . '/oauth/authorize',
    'server.token'    => getenv('DISCOVER_API_OAUTH_URL') . '/oauth/token',
    'entrypoint.data' => getenv('DISCOVER_API_OAUTH_URL') . '/projects/',
    'entrypoint.user' => getenv('DISCOVER_API_OAUTH_URL') . '/user/',
    'client.id'       => getenv('DISCOVER_API_OAUTH_ID'),
    'client.secret'   => getenv('DISCOVER_API_OAUTH_SESCRET'),
];
