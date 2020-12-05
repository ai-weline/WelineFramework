<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session\Driver;

interface DriverInterface
{
    const driver_FILE = 'file';

    const driver_REDIS = 'redis';

    public function __construct(array $config);
}
