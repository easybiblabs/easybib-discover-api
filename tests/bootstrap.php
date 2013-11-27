<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/src/app.php';

putenv('OAUTH_URL=http://localhost');
putenv('OAUTH_ID=test');
putenv('OAUTH_SECRET=test');


return $app;
