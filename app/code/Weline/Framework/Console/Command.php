<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console;

class Command extends CommandAbstract
{
    public function execute($args = [])
    {
        return '定位命令';
    }

    public function getTip(): string
    {
        return '定位命令';
    }
}
