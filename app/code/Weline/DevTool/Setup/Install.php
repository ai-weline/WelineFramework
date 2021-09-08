<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/5/22
 * 时间：11:06
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\DevTool\Setup;


use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Database\Db\Ddl\Create;
use Weline\Framework\Setup\Data;

class Install implements \Weline\Framework\Setup\InstallInterface
{
    const table_DEV_DOCUMENT = 'weline_dev_tool_document_catalog';
    const table_DEV_DOCUMENT_CONTENT = 'weline_dev_tool_document_catalog_content';

    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $db = $setup->getDb();
        $printer = $setup->getPrinter();
        // 如果表不存在就创建
        if (!$db->tableExist(self::table_DEV_DOCUMENT)) {
            $printer->warning(self::table_DEV_DOCUMENT.__('安装中...'));
            $db->createTable(self::table_DEV_DOCUMENT)
                ->addColumn(
                    'id',
                    TableInterface::column_type_INTEGER,
                    null,
                    'primary key NOT NULL AUTO_INCREMENT',
                    'ID'
                )->addColumn(
                    'name',
                    TableInterface::column_type_VARCHAR,
                    60,
                    'NOT NULL',
                    '分类名'
                )->addColumn(
                    'parent_id',
                    TableInterface::column_type_INTEGER,
                    null,
                    'DEFAULT NULL',
                    '父分类ID'
                )->create();
        }
    }
}