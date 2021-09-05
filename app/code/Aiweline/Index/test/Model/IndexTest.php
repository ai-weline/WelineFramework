<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\test\Model;


use Aiweline\Index\Model\Index;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class IndexTest extends TestCore
{
    private Index $indexModel;
    function setUp(): void
    {
        $this->indexModel = ObjectManager::getInstance(Index::class);
    }

    function testIndex(){
        p($this->indexModel->getTable());
    }
}