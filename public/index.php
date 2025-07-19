<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap application and get instance
$app = require_once __DIR__ . '/../app/bootstrap.php';

// Load routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// Run application
$app->run();