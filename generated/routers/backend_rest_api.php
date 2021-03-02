<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

return [
    'admin/rest/v1/index::GET' => [
        'module' => 'Weline_Admin',
        'class'  => [
            'area'           => 'BackendRestController',
            'name'           => 'Weline\\Admin\\Api\\Rest\\V1\\Index',
            'method'         => 'index',
            'request_method' => 'GET',
        ],
    ],
];
