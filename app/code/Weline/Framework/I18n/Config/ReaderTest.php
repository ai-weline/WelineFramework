<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\I18n\Config;

use Weline\Framework\Manager\ObjectManager;

class ReaderTest extends \Weline\Framework\UnitTest\TestCore
{
    public function test__construct()
    {
        $reader = ObjectManager::getInstance(Reader::class);
        p($reader);
    }
}
