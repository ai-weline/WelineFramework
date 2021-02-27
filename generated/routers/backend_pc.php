<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

return [
    'admin/index/index' => [
        'module' => 'Weline_Admin',
        'class'  => [
            'area'   => 'BackendController',
            'name'   => 'Weline\\Admin\\Controller\\Index',
            'method' => 'index',
        ],
    ],
    'admin/index/test' => [
        'module' => 'Weline_Admin',
        'class'  => [
            'area'   => 'BackendController',
            'name'   => 'Weline\\Admin\\Controller\\Index',
            'method' => 'test',
        ],
    ],
];
