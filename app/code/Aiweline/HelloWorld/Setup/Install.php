<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Setup;

use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Setup\Data;
use Weline\Framework\Setup\InstallInterface;

class Install implements InstallInterface
{
    const table_DEMO = 'aiweline_hello_world';

    /**
     * @DESC         |安装方法
     *
     * 参数区：
     *
     * @param Data\Setup $setup
     * @param Data\Context $context
     */
    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $context->getPrinter()->note('执行安装...');
        if (! $setup->getDb()->tableExist(self::table_DEMO)) {
            $setup->getDb()->createTable(
                self::table_DEMO,
                '开发测试'
            )->addColumn(
                'entity_id',
                Table::column_type_INTEGER,
                11,
                'primary key NOT NULL AUTO_INCREMENT',
                '实例ID'
            )->addColumn(
                'demo',
                Table::column_type_TEXT,
                256,
                'NOT NULL',
                '测试'
            )->addIndex(
                Table::index_type_DEFAULT,
                'id_index',
                'entity_id'
            )->addConstraints()->create();
        }
        $context->getPrinter()->note('安装脚本执行完毕...');
    }
}
