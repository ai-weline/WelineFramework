<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */


// 运行模式
defined('CLI') || define('CLI', PHP_SAPI === 'cli');
// 项目根目录
defined('BP') || define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 静态文件路径
defined('PUB') || define('PUB', BP . 'pub' . DIRECTORY_SEPARATOR);
// 应用 目录 (默认访问 web)
defined('APP_PATH') || define('APP_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR);
// 应用 配置 目录 (默认访问 etc)
defined('APP_ETC_PATH') || define('APP_ETC_PATH', APP_PATH. 'etc' . DIRECTORY_SEPARATOR);
// 检测自动加载
try {
    $autoloader = BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (@is_file($autoloader)) {
        require $autoloader;
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
