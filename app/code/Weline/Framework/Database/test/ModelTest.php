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
        p($this->model->find(1));
        p($this->model->load('id',1));
    }

    /**
     * @throws \ReflectionException
     * @throws \Weline\Framework\Exception\Core
     */
    function testSave()
    {
        # 模型不存在数据时自动插入
//        p($this->model->save(['id'=>6,'stores'=>441114]));
        # 模型存在数据时自动更新
//        $this->model = $this->model->load(6);
        $this->model->load(8);
//        p($this->model->where('id',8)->find()->fetch());
        p($this->model->getData());
//        $this->model->setId(8);
        $this->model->setData('stores',8888)->setData('id',7);
        p($this->model->save());// 处理id字符串时不更新问题

        p($this->model->save(['stores'=>8818]));
        p($this->model->save(['id'=>8,'stores'=>8818]));
    }
    function testWhere()
    {
        p($this->model->where([['id',6],['stores',666]])->find()->fetch());
    }
    function testUpdate()
    {
        $this->model->find(6);
        p($this->model->update(['stores'=>767])->fetch());
    }

}
