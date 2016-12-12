<?php
return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/resources/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/resources/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'env',
        'env' => [
            'adapter' => 'mysql',
            'host' => $_SERVER['PAYROLL_MYSQL_PORT_3306_TCP_ADDR'],
            'name' => $_SERVER['PAYROLL_MYSQL_ENV_MYSQL_DATABASE'],
            'user' => $_SERVER['PAYROLL_MYSQL_ENV_MYSQL_USER'],
            'pass' => $_SERVER['PAYROLL_MYSQL_ENV_MYSQL_PASSWORD'],
            'port' => $_SERVER['PAYROLL_MYSQL_PORT_3306_TCP_PORT'],
            'charset' => 'utf8',
        ],
        'testing' => [ 'adapter' => 'sqlite' ]
    ]
];
