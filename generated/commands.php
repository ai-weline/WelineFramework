<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

return [
    'article#Aiweline\\HelloWorld' => [
        'article:get' => 'getTip(111111111)',
    ],

    'command#Weline\\Framework' => [
        'command:upgrade' => '更新命令',
    ],
    'deploy:mode#Weline\\Framework' => [
        'deploy:mode:set'  => '部署模式设置。（dev:开发模式；prod:生产环境。）',
        'deploy:mode:show' => '查看部署环境',
    ],
    'deploy#Weline\\Framework' => [
        'deploy:upgrade' => '静态资源同步更新。',
    ],
    'dev#Weline\\Framework' => [
        'dev:debug' => '开发测试：用于运行测试对象！',
    ],
    'module#Weline\\Framework' => [
        'module:disable' => '禁用模块',
        'module:enable'  => '模块启用',
        'module:remove'  => '移除模块以及模块数据！并执行卸载脚本（如果有）',
        'module:status'  => '获取模块列表',
        'module:upgrade' => '升级模块',
    ],
    'setup:di#Weline\\Framework' => [
        'setup:di:compile' => 'DI依赖编译',
    ],
    'system:install#Weline\\Framework' => [
        'system:install:sample' => '安装脚本样例',
    ],
    'system#Weline\\Framework' => [
        'system:install' => '框架安装',
    ],
];
