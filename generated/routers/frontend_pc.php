<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

return [
    'hello/demo/index/index' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\Demo\\Index',
            'method' => 'index',
        ],
    ],
    'hello/helloworld/index' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'index',
        ],
    ],
    'hello/helloworld/p' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'p',
        ],
    ],
    'hello/helloworld/ex' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'ex',
        ],
    ],
    'hello/helloworld/model' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'model',
        ],
    ],
    'hello/helloworld/demo' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'demo',
        ],
    ],
    'hello/helloworld/session' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'session',
        ],
    ],
    'hello/helloworld/cache' => [
        'module' => 'Aiweline_HelloWorld',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\HelloWorld\\Controller\\HelloWorld',
            'method' => 'cache',
        ],
    ],
    'index/index' => [
        'module' => 'Aiweline_Index',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\Index\\Controller\\Index',
            'method' => 'index',
        ],
    ],
    'test/index' => [
        'module' => 'Aiweline_Index',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\Index\\Controller\\Test',
            'method' => 'index',
        ],
    ],
    'news/index/index' => [
        'module' => 'Aiweline_NewsSource',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\NewsSource\\Controller\\Index',
            'method' => 'index',
        ],
    ],
    'todo/ok' => [
        'module' => 'Aiweline_Test2',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Aiweline\\Test2\\Controller\\ToDo',
            'method' => 'ok',
        ],
    ],
    'admin/index/index' => [
        'module' => 'Weline_Admin',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Weline\\Admin\\Controller\\Index',
            'method' => 'index',
        ],
    ],
    'admin/index/test' => [
        'module' => 'Weline_Admin',
        'class'  => [
            'area'   => 'FrontendController',
            'name'   => 'Weline\\Admin\\Controller\\Index',
            'method' => 'test',
        ],
    ],
];
