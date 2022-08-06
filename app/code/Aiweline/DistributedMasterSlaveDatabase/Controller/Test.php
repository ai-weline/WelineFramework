<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\DistributedMasterSlaveDatabase\Controller;

use Weline\Framework\Manager\ObjectManager;
use Aiweline\DistributedMasterSlaveDatabase\Model\Test as TestModel;

class Test extends \Weline\Framework\App\Controller\FrontendController
{
    function index()
    {
        /**@var TestModel $testModel */
        $testModel = ObjectManager::getInstance(TestModel::class);
        return $testModel->select()->fetch();
    }
}