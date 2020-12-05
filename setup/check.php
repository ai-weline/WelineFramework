<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

// 安装检测
if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock')) {
    http_response_code(404);
    exit(0);
}
