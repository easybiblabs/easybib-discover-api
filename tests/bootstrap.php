<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/src/app.php';

putenv('DISCOVER_API_OAUTH_URL=http://localhost');
putenv('DISCOVER_API_OAUTH_ID=test');
putenv('DISCOVER_API_OAUTH_SECRET=test');


return $app;
