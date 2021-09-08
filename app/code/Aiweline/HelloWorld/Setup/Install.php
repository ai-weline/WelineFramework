<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Setup;

use Weline\Framework\Database\Api\Db\Ddl\Table\CreateInterface;
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
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Database\Exception\LinkException
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
            )->addConstraints()->create();
        }
        $context->getPrinter()->note('安装脚本执行完毕...');
    }
}
