<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */
// 检查安装
if (! file_exists(__DIR__ . '/../setup/install.lock')) {
    require __DIR__ . '/../setup/index.php';
    exit();
}
// 加载启动器
require __DIR__ . '/../app/bootstrap.php';