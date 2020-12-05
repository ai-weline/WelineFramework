<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Setup;

use Weline\Framework\Setup\Data;
use Weline\Framework\Setup\RemoveInterface;

class Remove implements RemoveInterface
{
    public function setup(Data\Setup $setup, Data\Context $context)
    {
        return 'Remove OK!';
    }
}
