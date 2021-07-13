<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

require 'check.php';

// 项目根目录
define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 应用目录
define('APP_PATH', BP . 'app' . DIRECTORY_SEPARATOR);
// 开发模式
define('DEV', true);
// CLI 环境
define('CLI', false);
// 应用 配置 目录 (默认访问 etc)
define('APP_ETC_PATH', APP_PATH . 'etc' . DIRECTORY_SEPARATOR);
// 自动加载
try {
    require BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}
// 助手函数
require BP . '/app/etc/functions.php';

/**
 * php bin/m system:install ^
 * --db-type=mysql ^
 * --db-hostname=127.0.0.1 ^
 * --db-database=weline ^
 * --db-username=weline ^
 * --db-password=weline
 */
