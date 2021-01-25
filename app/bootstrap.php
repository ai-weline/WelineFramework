<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

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
// 检测自动加载
try {
    require BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}
// 尝试加载应用
try {
    /**@var $app \Weline\Framework\App */
    $app = ObjectManager::getInstance(\Weline\Framework\App::class);
} catch (Exception $exception) {
    if (DEV) {
        exit('应用启动失败：' . $exception->getMessage());
    }
}
// 启动应用
$app->run();

if (DEV) {
    $exception = error_get_last();
//    if ($exception) {
//        p($exception->getMessage());
//    }
}
