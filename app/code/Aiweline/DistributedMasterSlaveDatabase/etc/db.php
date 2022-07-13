<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */
return [
    'type'   => 'mysql',
    'master' => [
        'hostname' => '127.0.0.1',
        'database' => 'app_master',
        'username' => 'app_master',
        'password' => 'app_master',
        'hostport' => '3306',
        'prefix'   => 'm_',
        'charset'  => 'utf8mb4',
        'type'     => 'mysql',
    ],
    'slaves' => [
        'slave1' => [
            'hostname' => '127.0.0.1',
            'database' => 'app_slave1',
            'username' => 'app_slave1',
            'password' => 'app_slave1',
            'hostport' => '3306',
            'prefix'   => 'm_',
            'charset'  => 'utf8mb4',
            'type'     => 'mysql',
        ],
        'slave2' => [
            'hostname' => '127.0.0.1',
            'database' => 'app_slave2',
            'username' => 'app_slave2',
            'password' => 'app_slave2',
            'hostport' => '3306',
            'prefix'   => 'm_',
            'charset'  => 'utf8mb4',
            'type'     => 'mysql',
        ],
    ]
];
