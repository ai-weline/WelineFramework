<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\test;

use Weline\Framework\Database\test\ModelTest\Model;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class ModelTest extends TestCore
{
    function testProcessTable()
    {
        /**@var Model $model */
        $model = ObjectManager::getInstance(Model::class);
        p($model->processTable());
    }
}
