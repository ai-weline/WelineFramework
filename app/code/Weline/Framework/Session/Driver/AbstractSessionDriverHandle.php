<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session\Driver;

use Weline\Framework\Session\SessionInterface;

abstract class AbstractSessionDriverHandle implements SessionInterface, DriverInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        session_set_save_handler(
            [&$this, 'open'],
            [&$this, 'del'],
            [&$this, 'set'],
            [&$this, 'get'],
            [&$this, 'des'],
            [&$this, 'gc']
        );
    }
}
