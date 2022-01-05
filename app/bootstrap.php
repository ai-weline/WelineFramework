<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */
// ############################# 系统配置 #####################
// 执行时间
define('START_TIME', microtime(true));
// 项目根目录
defined('BP') || define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 运行模式
defined('CLI') || define('CLI', PHP_SAPI === 'cli');
// 检查安装
if (!CLI and !file_exists(BP . 'setup/install.lock')) {
    require BP . 'setup/index.php';
    exit();
}
// 系统是否WIN
define('IS_WIN', strtolower(substr(PHP_OS, 0, 3)) === 'win');
// 静态文件路径
defined('PUB') || define('PUB', BP . 'pub' . DIRECTORY_SEPARATOR);

// ############################# Composer代理配置 #####################
// 第三方代码目录
defined('VENDOR_PATH') || define('VENDOR_PATH', BP . 'vendor' . DIRECTORY_SEPARATOR);
// 检测Composer自动加载代理
try {
    $autoloader = VENDOR_PATH . 'autoload.php';
    if (is_file($autoloader)) {
        require $autoloader;
    } else {
        exit('Composer自动加载异常!尝试执行：php composer.phar install');
    }
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}
// ############################# 应用相关配置 #####################
// 应用 目录 (默认访问 web)
defined('APP_PATH') || define('APP_PATH', BP . 'app' . DIRECTORY_SEPARATOR);
defined('APP_CODE_PATH') || define('APP_CODE_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR);
// 应用配置文件
if (is_file(APP_CODE_PATH . 'config.php')) require APP_CODE_PATH . 'config.php';
// 主题 目录
defined('APP_DESIGN_PATH') || define('APP_DESIGN_PATH', APP_CODE_PATH . 'design' . DIRECTORY_SEPARATOR);
// 静态 目录
defined('APP_STATIC_PATH') || define('APP_STATIC_PATH', PUB . 'static' . DIRECTORY_SEPARATOR);
// 应用 配置 目录 (默认访问 etc)
defined('APP_ETC_PATH') || define('APP_ETC_PATH', BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR);

// 调试模式
defined('DEBUG') || define('DEBUG', 0);


// ############################# 环境配置 #####################
// 环境
$config = [];
$env_filename = __DIR__ . '/etc/env.php';
if (is_file($env_filename)) {
    $config = require $env_filename;
}
// 助手函数
$handle_functions = BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'functions.php';
if (is_file($handle_functions)) {
    require BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'functions.php';
}

// 调试模式
define('DEV', 'dev' === $config['deploy']);
define('PROD', 'prod' === $config['deploy']);
//defined('DEV') || define('DEV', false);
//defined('PROD') || define('PROD', false);
// 调试模式
define('PHP_CS', $config['php-cs']);
//defined('PHP_CS') || define('PHP_CS', false);
//报告错误
DEBUG ? error_reporting(E_ALL) : error_reporting(0);

// 错误报告
if (DEV || CLI) {
    ini_set('error_reporting', E_ALL);
    register_shutdown_function(function () {
        $_error = error_get_last();
        if ($_error && in_array($_error['type'], [1, 4, 16, 64, 256, 4096, E_ALL], true)) {
            if (CLI) {
                echo __('致命错误：') . PHP_EOL;
                echo __('文件：') . $_error['file'] . PHP_EOL;
                echo __('行数：') . $_error['line'] . PHP_EOL;
                echo __('消息：') . $_error['message'] . PHP_EOL;
            } else {
                echo '<b style="color: red">致命错误：</b></br>';
                echo '<pre>';
                echo __('文件：') . $_error['file'] . '</br>';
                echo __('行数：') . $_error['line'] . '</br>';
                echo __('消息：') . $_error['message'] . '</br>';
                echo '</pre>';
            }
        }
    });
}

// ############################# 初始化应用... #####################
// 尝试加载应用
try {
    /**
     * 初始化应用...
     */
    \Weline\Framework\App::run();
} catch (Exception $exception) {
    if (DEBUG || DEV) {
        echo '<pre>';
        echo '应用启动失败：<b style="color: red">' . $exception->getMessage() . '</b>' . PHP_EOL;
        echo '错误信息：' . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
        var_dump($exception->getTrace());
        exit(0);
    } else {
        echo '<pre>';
        echo '<b style="color: red">系统异常，请联系网站管理员进行修复！</b>';
        exit(0);
    }
}