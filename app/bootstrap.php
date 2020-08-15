<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/9
 * 时间：21:23
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

use M\Framework\App;
use M\Framework\App\Env;
use M\Framework\Manager\ObjectManager;

// 项目根目录
define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 框架根目录
define('FP', BP . 'M' . DIRECTORY_SEPARATOR);
// 静态文件路径
define('PUB', BP . 'pub' . DIRECTORY_SEPARATOR);
// 应用 目录 (默认访问 web)
define('APP_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR);
// 应用 配置 目录 (默认访问 etc)
define('APP_ETC_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR);
// 自动加载
try {
    require BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';;
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}

// 尝试加载应用
try {
    /**@var $app App */
    $app = ObjectManager::getInstance(App::class);
} catch (Exception $exception) {
    exit('应用启动失败：' . $exception->getMessage());
}

// 助手函数
require BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'functions.php';
// 调试模式
(Env::getInstance()->getConfig('deploy') == 'dev') ? define('DEV', true) : define('DEV', false);
//报告错误
DEV ? error_reporting(E_ALL) : error_reporting(0);
//error_reporting(E_ALL);
// 启动应用
$app->run();