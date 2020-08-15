<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/4
 * 时间：22:07
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */
require 'check.php';
// 项目根目录
define('BP', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 框架根目录
define('FP', BP . 'M' . DIRECTORY_SEPARATOR);
// 开发模式
define('DEV', true);
// 应用 配置 目录 (默认访问 etc)
define('APP_ETC_PATH', BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR);
// 自动加载
try {
    require BP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
} catch (Exception $exception) {
    exit('自动加载异常：' . $exception->getMessage());
}

// 助手函数
require BP . '/app/etc/functions.php';
error_reporting(E_ALL);
//error_reporting(0);