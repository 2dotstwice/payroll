<?php
define('DEBUG', false);
$app = require __DIR__ . '/../bootstrap.php';
$app['http_cache']->run();
