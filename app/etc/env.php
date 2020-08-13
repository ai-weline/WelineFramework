<?php return array (
  'admin' => 'admin123',
  'api_admin' => 'rest_admin',
  'deploy' => 'dev',
  'db' => [],
  'log' => 
  array (
    'error' => '/www/wwwroot/m.aiweline.com/var/log/error.log',
    'exception' => '/www/wwwroot/m.aiweline.com/var/log/exception.log',
    'notice' => '/www/wwwroot/m.aiweline.com/var/log/notice.log',
    'warning' => '/www/wwwroot/m.aiweline.com/var/log/warning.log',
    'debug' => '/www/wwwroot/m.aiweline.com/var/log/debug.log',
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
);