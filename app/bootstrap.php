<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

// 检查安装
if ('cli' !== PHP_SAPI and !file_exists(dirname(__DIR__) . '/setup/install.lock')) {
    require dirname(__DIR__) . '/setup/index.php';
    exit();
}
$start_time = microtime(true);
// 项目根目录
defined('BP') || define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 应用 目录 (默认访问 web)
defined('APP_PATH') || define('APP_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR);
if(is_file(APP_PATH.'/config.php'))require APP_PATH.'/config.php';
// 运行模式
defined('CLI') || define('CLI', PHP_SAPI === 'cli');
// 调试模式
defined('DEBUG') || define('DEBUG', 0);
// 静态文件路径
defined('PUB') || define('PUB', BP . 'pub' . DIRECTORY_SEPARATOR);
// 主题 目录
defined('APP_DESIGN_PATH') || define('APP_DESIGN_PATH', APP_PATH . 'design' . DIRECTORY_SEPARATOR);
// 静态 目录
defined('APP_STATIC_PATH') || define('APP_STATIC_PATH', PUB . 'static' . DIRECTORY_SEPARATOR);
// 应用 配置 目录 (默认访问 etc)
defined('APP_ETC_PATH') || define('APP_ETC_PATH', BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR);
// 检测自动加载
try {
    $autoloader = BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($autoloader)) {
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
//    if (DEV) {
        echo '<pre>';
        echo '应用启动失败：' . $exception->getMessage().PHP_EOL;
        if(DEV)echo '错误信息：'.PHP_EOL . $exception->getTraceAsString().PHP_EOL;
        if(DEV)var_dump($exception->getTrace());
        exit(0);
//    }
}