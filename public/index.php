<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/functions.php';

define('APP_DEBUG', true);

$app = new \app\Application();
$app->run('web');

