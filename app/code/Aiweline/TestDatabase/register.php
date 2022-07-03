<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

use Weline\Framework\Register\Register;
use Weline\Framework\Register\RegisterDataInterface;

Register::register(
    RegisterDataInterface::MODULE,
    'Aiweline_TestDatabase',
    __DIR__,
    '1.0.0',
    '测试读写分离和应用级别数据库'
);
