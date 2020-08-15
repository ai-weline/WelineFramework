<?php return array (
  'session' => 
  array (
    'default' => 'file',
    'drivers' => 
    array (
      'file' => 
      array (
        'path' => 'var/session/',
      ),
      'mysql' => 
      array (
        'tip' => '开发中...',
      ),
      'redis' => 
      array (
        'tip' => '开发中...',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'drivers' => 
    array (
      'file' => 
      array (
        'path' => 'var/cache/',
      ),
      'redis' => 
      array (
        'tip' => '开发中...',
        'server' => '127.0.0.1',
        'port' => 6379,
        'database' => 1,
      ),
    ),
  ),
  'log' => 
  array (
    'error' => '/www/wwwroot/m.dev.aiweline.com/var/log/error.log',
    'exception' => '/www/wwwroot/m.dev.aiweline.com/var/log/exception.log',
    'notice' => '/www/wwwroot/m.dev.aiweline.com/var/log/notice.log',
    'warning' => '/www/wwwroot/m.dev.aiweline.com/var/log/warning.log',
    'debug' => '/www/wwwroot/m.dev.aiweline.com/var/log/debug.log',
  ),
  'admin' => 'admin_123',
  'api_admin' => 'rest_123',
  'deploy' => 'dev',
  'db' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'mysql' => 
      array (
        'hostname' => '127.0.0.1',
        'database' => 'weline',
        'username' => 'weline',
        'password' => 'weline',
        'hostport' => '3306',
        'prefix' => 'm_',
        'charset' => 'utf8',
        'type' => 'mysql',
      ),
    ),
  ),
);