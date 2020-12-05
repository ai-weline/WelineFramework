<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

use Weline\Framework\App\Env;
use Weline\Framework\Manager\ObjectManager;

// 运行模式
define('CLI', PHP_SAPI === 'cli');
// 项目根目录
define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 静态文件路径
define('PUB', BP . 'pub' . DIRECTORY_SEPARATOR);
// 应用 目录 (默认访问 web)
define('APP_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR);
// 应用 配置 目录 (默认访问 etc)
define('APP_ETC_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR);
// 执行时间
define('START_TIME', microtime(true));

// 系统是否WIN
define('IS_WIN', strtolower(substr(PHP_OS, 0, 3)) === 'win');
// 自动加载
try {
    require BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}
// 助手函数
require BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'functions.php';
// 调试模式
(Env::getInstance()->getConfig('deploy') === 'dev') ? define('DEV', true) : define('DEV', false);
//报告错误
DEV ? error_reporting(E_ALL) : error_reporting(0);
//error_reporting(E_ALL);
// 尝试加载应用
try {
    /**@var $app \Weline\Framework\App */
    $app = ObjectManager::getInstance(\Weline\Framework\App::class);
    // 启动应用
    $app->run();
} catch (Exception $exception) {
    exit('应用启动失败：' . $exception->getMessage());
}
