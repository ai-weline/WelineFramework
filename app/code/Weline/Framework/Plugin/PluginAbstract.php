<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

abstract class PluginAbstract implements \Weline\Framework\Plugin\Api\Data\PluginInterface
{
    public function orderNumber(): int
    {
        return 1;
    }

    public function isEnable(): bool
    {
        return true;
    }
}
