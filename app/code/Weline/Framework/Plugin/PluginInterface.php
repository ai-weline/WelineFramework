<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

interface PluginInterface
{
    const dir = 'Plugin';

    /**
     * @DESC         |设置插件顺序
     *
     * 参数区：
     *
     * @return int
     */
    public function orderNumber(): int;

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return bool
     */
    public function isEnable(): bool;
}
