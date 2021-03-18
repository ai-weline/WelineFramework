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
    'cache#Weline\\Framework\\Cache' => [
        'cache:clear' => '缓存清理。',
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
    'event:cache#Weline\\Framework\\Event' => [
        'event:cache:clear' => '清除系统事件缓存！',
        'event:cache:flush' => '刷新系统事件缓存！',
    ],
    'event#Weline\\Framework\\Event' => [
        'event:cache' => '事件缓存管理！-c：清除缓存；-f：刷新缓存。',
        'event:data'  => '事件观察者列表！',
    ],
    'plugin:cache#Weline\\Framework\\Plugin' => [
        'plugin:cache:clear' => '插件缓存清理！',
    ],
    'plugin:di#Weline\\Framework\\Plugin' => [
        'plugin:di:compile' => '系统依赖编译',
    ],
    'plugin:status#Weline\\Framework\\Plugin' => [
        'plugin:status:set' => '状态操作：0/1 0:关闭，1:启用',
    ],
];
