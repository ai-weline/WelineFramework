<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

return [
    'bbs/rest/v1/index/index::GET' => [
        'module' => 'Aiweline_Bbs',
        'class'  => [
            'area'           => 'FrontendRestController',
            'name'           => 'Aiweline\\Bbs\\Api\\Rest\\V1\\Index',
            'method'         => 'getIndex',
            'request_method' => 'GET',
        ],
    ],
    'bbs/rest/v1/index/index::POST' => [
        'module' => 'Aiweline_Bbs',
        'class'  => [
            'area'           => 'FrontendRestController',
            'name'           => 'Aiweline\\Bbs\\Api\\Rest\\V1\\Index',
            'method'         => 'postIndex',
            'request_method' => 'POST',
        ],
    ],
    'bbs/rest/v1/index/index::PUT' => [
        'module' => 'Aiweline_Bbs',
        'class'  => [
            'area'           => 'FrontendRestController',
            'name'           => 'Aiweline\\Bbs\\Api\\Rest\\V1\\Index',
            'method'         => 'putIndex',
            'request_method' => 'PUT',
        ],
    ],
    'bbs/rest/v1/index/index::DELETE' => [
        'module' => 'Aiweline_Bbs',
        'class'  => [
            'area'           => 'FrontendRestController',
            'name'           => 'Aiweline\\Bbs\\Api\\Rest\\V1\\Index',
            'method'         => 'deleteIndex',
            'request_method' => 'DELETE',
        ],
    ],
    'bbs/rest/v1/index/index::UPDATE' => [
        'module' => 'Aiweline_Bbs',
        'class'  => [
            'area'           => 'FrontendRestController',
            'name'           => 'Aiweline\\Bbs\\Api\\Rest\\V1\\Index',
            'method'         => 'updateIndex',
            'request_method' => 'UPDATE',
        ],
    ],
];
