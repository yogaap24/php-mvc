<?php

use Yogaap\PHP\MVC\App\Router;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../routes/web.php';

Router::run();