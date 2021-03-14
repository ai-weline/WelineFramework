<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

ini_set('error_reporting', E_ALL);

function cache_shutdown_error()
{
    $_error = error_get_last();
    if ($_error && in_array($_error['type'], [1, 4, 16, 64, 256, 4096, E_ALL], true)) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<b style="color: red">可以尝试 composer update 更新本地依赖包。</b></br>';
        echo '<b style="color: red">致命错误：</b></br>';
        echo '<pre>';
        echo $_error['message'];
        echo '</pre>';
    }
}
register_shutdown_function('cache_shutdown_error');

// 安装检测
if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock')) {
    http_response_code(404);
    exit(0);
}
