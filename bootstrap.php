<?php
require_once __DIR__.'/vendor/autoload.php';
define('STORAGE_PATH', __DIR__.'/storage/', true);

use Silex\Application;
use Silex\Provider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

date_default_timezone_set('Europe/Brussels');

$app = new Application();

require_once __DIR__.'/resources/config/default.php';

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => $app['db.options']
));
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => STORAGE_PATH.'/cache/',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/resources/views',
));

$app->register(new Provider\HttpFragmentServiceProvider());

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => STORAGE_PATH.'/logs/'.date('Y-m-d').'.log',
    'monolog.level' => DEBUG ? Monolog\Logger::DEBUG : Monolog\Logger::ERROR,
    'monolog.name' => 'application'
));
$app->before(function (Request $request) use ($app) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        // TODO: this probably only works when posting json, expand this so it works for any api call, also get
        $app->error(function (\Exception $e, $code) use ($app) {
            $app['monolog']->addError($e->getMessage());
            $app['monolog']->addError($e->getTraceAsString());
            return new JsonResponse(array('statusCode' => $code, 'message' => $e->getMessage()));
        });
    }
});

/* oauth */
$app->register(new Silex\Provider\RoutingServiceProvider());
// Provides session storage
$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => STORAGE_PATH.'/sessions'
));
$app->register(new Gigablah\Silex\OAuth\OAuthServiceProvider(), array(
    'oauth.services' => array(
        'Google' => array(
            'key' => $app['oauth.google']['clientID'],
            'secret' => $app['oauth.google']['secret'],
            'scope' => array(
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ),
            'user_endpoint' => 'https://www.googleapis.com/oauth2/v1/userinfo'
        )
    )
));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => '^/.*',
            'anonymous' => true,
            'oauth' => array(
                'login_path' => '/auth/{service}',
                'callback_path' => '/auth/{service}/callback',
                'check_path' => '/auth/{service}/check',
                'failure_path' => '/login',
                'with_csrf' => true
            ),
            'logout' => array(
                'logout_path' => '/logout',
                'with_csrf' => true
            ),
            // OAuthInMemoryUserProvider returns a StubUser and is intended only for testing.
            // Replace this with your own UserProvider and User class.
            //'users' => new Gigablah\Silex\OAuth\Security\User\Provider\OAuthInMemoryUserProvider()
            'users' => new \TwoDotsTwice\Payroll\OAuthPayrollUserProvider()
        ),
    ),
    'security.access_rules' => array(
        array('^/auth', 'ROLE_USER')
    )
));
// Provides CSRF token generation
$app->register(new Silex\Provider\FormServiceProvider());
$app->before(function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
    if (isset($app['security.token_storage'])) {
        $token = $app['security.token_storage']->getToken();
    } else {
        $token = $app['security']->getToken();
    }

    $app['user'] = null;

    if ($token && !$app['security.trust_resolver']->isAnonymous($token)) {
        $app['user'] = $token->getUser();
    }
});
$app->get('/login', function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $services = array_keys($app['oauth.services']); // TODO: why is this necessary?

    return $app['twig']->render('login.twig', array(
        'login_paths' => $app['oauth.login_paths'],
        'logout_path' => $app['url_generator']->generate('logout', array(
            '_csrf_token' => $app['oauth.csrf_token']('logout')
        )),
        'error' => $app['security.last_error']($request)
    ));
});
$app->match('/logout', function () {})->bind('logout');
/* /oauth */

$app->match('/auth-test', function () {
    return 'Testing auth';
});

$app->get('/app', function (Request $request) use ($app) {
    return $app['twig']->render('app.twig', array(
        'login_paths' => $app['oauth.login_paths'],
        'logout_path' => $app['url_generator']->generate('logout', array(
            '_csrf_token' => $app['oauth.csrf_token']('logout')
        )),
        'error' => $app['security.last_error']($request)
    ));
});

$app->get('/', function () use ($app) {
    /* oauth */
    if (!is_null($app['user'])) {
        if ($app['user']->getEmailDomain() === '2dotstwice.be') {
            return $app->redirect('/app');
        }
    }
    return $app['twig']->render('index.twig', array(
        'login_paths' => $app['oauth.login_paths'],
        'logout_path' => $app['url_generator']->generate('logout', array(
            '_csrf_token' => $app['oauth.csrf_token']('logout')
        ))
    ));
    /* /oauth */
});

$app->register(new Quazardous\Silex\Provider\ConsoleServiceProvider, [
    'db.migrations.path' => './resources/migrations',
]);

$app['users.service'] = function() use ($app) {
    return new TwoDotsTwice\Payroll\UserService($app['db']);
};
$app['verlofdagen.service'] = function() use ($app) {
    return new TwoDotsTwice\Payroll\VerlofdagService($app['db']);
};
$app['users.controller'] = function() use ($app) {
    return new TwoDotsTwice\Payroll\Api\UserController($app['users.service']);
};
$app['verlofdagen.controller'] = function() use ($app) {
    return new TwoDotsTwice\Payroll\Api\VerlofdagController($app['users.service'], $app['verlofdagen.service']);
};

$api = $app['controllers_factory'];

// users
$api->get('/users', 'users.controller:getAll');
$api->get('/users/{id}', 'users.controller:getOne');
$api->post('/users', 'users.controller:save');
$api->put('/users/{id}', 'users.controller:update');
$api->delete('/users/{id}', 'users.controller:delete');

// verlofdagen
$api->get('/verlofdagen', 'verlofdagen.controller:getAll');


$app->mount('/api/v1', $api);

return $app;