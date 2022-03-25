<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Resource\Compiler;

class Less extends Compiler
{
    public function __init()
    {
        $this->setReader(ObjectManager::getInstance(\Weline\Theme\Config\Reader\Less::class));
    }

    public function compile(string $source_file = null, string $out_file = null)
    {
        p('等待编译开发...', 1);
    }
}
