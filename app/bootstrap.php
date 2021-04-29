<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
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
    if (is_file(BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        require BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    } else {
        exit('Composer自动加载异常!尝试执行：php composer.phar install');
    }
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}

// 尝试加载应用
try {
    /**
     * 初始化应用...
     */
    \Weline\Framework\App::run();
} catch (Exception $exception) {
    if (DEV) {
        exit('应用启动失败：' . $exception->getMessage());
    }
}

//
//if (DEV) {
//    $exception = error_get_last();
//    if ($exception) {
//        /**@var $printing \Weline\Framework\Output\Cli\Printing */
//        $printing = ObjectManager::getInstance(\Weline\Framework\Output\Cli\Printing::class);
//        $printing->error($exception['message']);
//    }
//}
