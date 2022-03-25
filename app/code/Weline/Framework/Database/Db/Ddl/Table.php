<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Db\Ddl;

use Weline\Framework\Database\Api\Db;
use Weline\Framework\Database\Db\Ddl\Table\Create;
use Weline\Framework\Database\Db\Ddl\Table\Alter;
use Weline\Framework\Manager\ObjectManager;

class Table implements Db\TableInterface
{
    public function createTable(): \Weline\Framework\Database\Api\Db\Ddl\Table\CreateInterface
    {
        return ObjectManager::getInstance(Create::class);
    }

    public function alterTable(): \Weline\Framework\Database\Api\Db\Ddl\Table\AlterInterface
    {
        return ObjectManager::getInstance(Alter::class);
    }
}
