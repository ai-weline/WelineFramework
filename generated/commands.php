<?php return array (
  'article#Aiweline\\HelloWorld' => 
  array (
    'article:get' => 'getTip(111111111)',
  ),
  'cache#Weline\\Framework\\Cache' => 
  array (
    'cache:clear' => '缓存清理。',
  ),
  'command#Weline\\Framework' => 
  array (
    'command:upgrade' => '更新命令',
  ),
  'deploy:mode#Weline\\Framework' => 
  array (
    'deploy:mode:set' => '部署模式设置。（dev:开发模式；prod:生产环境。）',
    'deploy:mode:show' => '查看部署环境',
  ),
  'deploy#Weline\\Framework' => 
  array (
    'deploy:upgrade' => '静态资源同步更新。',
  ),
  'dev#Weline\\Framework' => 
  array (
    'dev:debug' => '开发测试：用于运行测试对象！',
  ),
  'module#Weline\\Framework' => 
  array (
    'module:disable' => '禁用模块',
    'module:enable' => '模块启用',
    'module:remove' => '移除模块以及模块数据！并执行卸载脚本（如果有）',
    'module:status' => '获取模块列表',
    'module:upgrade' => '升级模块',
  ),
  'setup:di#Weline\\Framework' => 
  array (
    'setup:di:compile' => 'DI依赖编译',
  ),
  'system:install#Weline\\Framework' => 
  array (
    'system:install:sample' => '安装脚本样例',
  ),
  'system#Weline\\Framework' => 
  array (
    'system:install' => '框架安装',
  ),
  'event:cache#Weline\\Framework\\Event' => 
  array (
    'event:cache:clear' => '清除系统事件缓存！',
    'event:cache:flush' => '刷新系统事件缓存！',
  ),
  'event#Weline\\Framework\\Event' => 
  array (
    'event:cache' => '事件缓存管理！-c：清除缓存；-f：刷新缓存。',
    'event:data' => '事件观察者列表！',
  ),
  'plugin:cache#Weline\\Framework\\Plugin' => 
  array (
    'plugin:cache:clear' => '插件缓存清理！',
  ),
  'plugin:di#Weline\\Framework\\Plugin' => 
  array (
    'plugin:di:compile' => '系统依赖编译',
  ),
  'plugin:status#Weline\\Framework\\Plugin' => 
  array (
    'plugin:status:set' => '状态操作：0/1 0:关闭，1:启用',
  ),
);