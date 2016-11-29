<?php
// if run from cli, using: php -S 0.0.0.0:8080 -t web web/index_dev.php
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// set debug to true
define('DEBUG', true, true);

$app = require __DIR__ . '/../bootstrap.php';
use Silex\Provider;

// enable debugging
Symfony\Component\Debug\Debug::enable();
$app['debug'] = true;

// Silex WebProfiler: https://github.com/silexphp/Silex-WebProfiler

$app->register(new Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => STORAGE_PATH.'/cache/profiler',
    'profiler.mount_prefix' => '/_profiler'
));

$app->run();
