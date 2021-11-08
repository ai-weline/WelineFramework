<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session\Driver;

use Weline\Framework\Session\SessionInterface;

abstract class AbstractSessionDriverHandle implements SessionInterface, DriverInterface,\SessionHandlerInterface
{
    private function __clone()
    {
    }
    public function __construct(array $config)
    {
        session_set_save_handler($this,true);
    }
}
