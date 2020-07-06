<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/28
 * 时间：13:38
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Setup;


use M\Framework\App\Exception;
use M\Framework\Database\Db\Ddl\Table;
use M\Framework\Setup\Data;
use M\Framework\Setup\InstallInterface;

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
     * @return string
     * @throws Exception
     */
    function setup(Data\Setup $setup, Data\Context $context)
    {
        $context->getPrinter()->note('执行安装...');
        $setup->getDb()
            ->createTable(
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
        $context->getPrinter()->note('安装脚本执行完毕...');
    }
}