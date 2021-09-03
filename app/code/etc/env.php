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
    'default' => 'frontend',
    'connections' => 
    array (
      'frontend' => 
      array (
        'hostname' => '127.0.0.1',
        'database' => 'weline',
        'username' => 'weline',
        'password' => 'weline',
        'type' => 'mysql',
        'hostport' => '3306',
        'prefix' => 'm_',
        'charset' => 'utf8',
      ),
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
  'admin' => 'admin_612e37e14d732',
  'api_admin' => 'api_612e37e14d734',
  'theme' => 
  array (
    'id' => '3',
    'name' => 'test',
    'path' => 'Weline\\test',
    'parent_id' => NULL,
    'is_active' => '1',
    'create_time' => '2021-09-03 19:29:54',
    'update_time' => '2021-09-03 19:29:54',
  ),
);