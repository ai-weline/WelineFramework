<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\DistributedMasterSlaveDatabase\Model;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Test extends \Weline\Framework\Database\Model
{
    const fields_NAME = 'name';

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
        $setup->dropTable();
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
        if($setup->tableExist()){
            return;
        }
        $setup->createTable()
              ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key', '测试ID')
              ->addColumn(self::fields_NAME, TableInterface::column_type_VARCHAR, 255, '', '测试名称')
              ->create();
    }
}
