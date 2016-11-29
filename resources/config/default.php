<?php
if (isset($_ENV['DATABASE_URL'])) {
    // dokku/heroku way of working
    $app['db.options'] = array( 'url' => $_ENV['DATABASE_URL'] );
}
elseif (isset($_SERVER['PAYROLL_MYSQL_ENV_MYSQL_DATABASE'])) {
    // vagrant/docker way of working
    $app['db.options'] = array( 'url' =>
        sprintf('mysql://%s:%s@%s:%u/%s',
            $_SERVER['PAYROLL_MYSQL_ENV_MYSQL_USER'],
            $_SERVER['PAYROLL_MYSQL_ENV_MYSQL_PASSWORD'],
            $_SERVER['PAYROLL_MYSQL_PORT_3306_TCP_ADDR'],
            $_SERVER['PAYROLL_MYSQL_PORT_3306_TCP_PORT'],
            $_SERVER['PAYROLL_MYSQL_ENV_MYSQL_DATABASE'])
    );
}
else {
    $app['db.options'] = array( 'url' => 'sqlite:///' . STORAGE_PATH . '/app.sqlite' );
}

// copy and alter in local.php
$app['oauth.google'] = array(
    'clientID' => 'client-id',
    'secret' =>   'secret'
);

// local config that are ignored by version control
if (file_exists(__DIR__.'/local.php')) {
    require 'local.php';
}
