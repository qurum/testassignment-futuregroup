<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'sqlite',
            'name' => $_ENV['NOTEBOOKS_DB'],
            'suffix' => '',
        ],
        'testing' => [
            'adapter' => 'sqlite',
            'name' => $_ENV['NOTEBOOKS_TEST_DB'],
            'suffix' => '',
        ],
    ],
    'version_order' => 'creation'
];
