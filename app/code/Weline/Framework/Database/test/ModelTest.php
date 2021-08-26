<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\test;

use Weline\Framework\Database\test\ModelTest\WelineModel;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class ModelTest extends TestCore
{
    private WelineModel $model;
    function setUp(): void
    {
        $this->model = ObjectManager::getInstance(WelineModel::class);
    }

    function testProcessTable()
    {
        p(data: $this->model->processTable());
    }

    function testLoad()
    {
        p($this->model->load('id',1));
    }
    function testSave()
    {
        # 模型不存在数据时自动插入
//        p($this->model->save(['id'=>6,'stores'=>441114]));
        # 模型存在数据时自动更新
//        $this->model = $this->model->load(6);
        $this->model->setData('id',6);
        p($this->model->save(['id'=>6,'stores'=>441114]));
    }
}
