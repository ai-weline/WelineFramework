<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Model;

use Weline\Framework\Database\Api\Db\Ddl\Table\CreateInterface;
use Weline\Framework\Database\Model;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class AiwelineHelloWorld extends Model
{
    public string $_primary_key='entity_id';
    public function setup(ModelSetup $setup, Context $context): void
    {
        $setup->dropTable();
        $this->install($setup, $context);
    }

    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    public function install(ModelSetup $setup, Context $context): void
    {
        if ($setup->tableExist()) {
            return;
        }
        $context->getPrinter()->note('执行安装...');
        $setup->createTable(
            $setup->getTable(),
            '开发测试'
        )->addColumn(
            'entity_id',
            CreateInterface::column_type_INTEGER,
            11,
            'primary key NOT NULL AUTO_INCREMENT',
            '实例ID'
        )->addColumn(
            'demo',
            CreateInterface::column_type_TEXT,
            256,
            'NOT NULL',
            '测试'
        )->addConstraints()
         ->create();
        $context->getPrinter()->note('安装脚本执行完毕...');
    }
}
