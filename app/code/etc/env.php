<?php return array (
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
  'log' => 
  array (
    'error' => 'var\\log\\error.log',
    'exception' => 'var\\log\\exception.log',
    'notice' => 'var\\log\\notice.log',
    'warning' => 'var\\log\\warning.log',
    'debug' => 'var\\log\\debug.log',
  ),
  'php-cs' => true,
  'db' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'mysql' => 
      array (
        'hostname' => '192.168.1.3',
        'database' => 'weline',
        'username' => 'weline',
        'password' => 'weline',
        'type' => 'mysql',
        'hostport' => '3306',
        'prefix' => 'm_',
        'charset' => 'utf8',
      ),
    ),
  ),
  'deploy' => 'dev',
);