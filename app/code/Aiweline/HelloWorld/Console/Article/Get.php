<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Console\Article;

use Weline\Framework\Console\CommandInterface;

class Get implements CommandInterface
{
    public function execute($args = [])
    {
        exit(' // TODO: Implement execute() method.');
    }

    public function getTip(): string
    {
        return 'getTip(111111111)';
    }
}
