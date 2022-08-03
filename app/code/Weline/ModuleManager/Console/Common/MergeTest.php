<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\ModuleManager\Console\Common;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class MergeTest extends TestCore
{
    function testExecute(){
        $result = ObjectManager::getInstance(Merge::class)->execute();
        p($result);
    }
}
