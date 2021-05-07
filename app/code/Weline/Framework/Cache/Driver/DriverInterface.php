<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Driver;

interface DriverInterface
{
    const driver_FILE = 'file';

    const driver_REDIS = 'redis';

    public function __construct(string $identity, array $config);
}
