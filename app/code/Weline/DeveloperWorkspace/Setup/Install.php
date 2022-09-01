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

namespace Weline\DeveloperWorkspace\Setup;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Database\Db\Ddl\Create;
use Weline\Framework\Database\Helper\Importer\SqlFile;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data;
use Weline\Framework\System\File\Scan;

class Install implements \Weline\Framework\Setup\InstallInterface
{
    public const table_DEV_DOCUMENT = 'weline_dev_tool_document_catalog';
    public const table_DEV_DOCUMENT_CONTENT = 'weline_dev_tool_document_catalog_content';

    private Scan $scan;

    public function __construct(
        Scan        $scan
    )
    {
        $this->scan = $scan;
    }
    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $db = $setup->getDb();
        $printer = $setup->getPrinter();
        // 如果表不存在就创建
        if (!$db->tableExist(self::table_DEV_DOCUMENT)) {
            $printer->warning(self::table_DEV_DOCUMENT . __('安装中...'));
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

        $sql_files = [];
        $this->scan->globFile($context->getModulePath() . 'Setup' . DS. 'Data' . DS.'sql'. DS.'*',$sql_files,'.sql');
        foreach($sql_files as $sql_file) {
            if (is_file($sql_file)) {
                $context->getPrinter()->setup('数据库Sql文件导入中...');
                /**@var SqlFile $sqlFile */
                $sqlFile = ObjectManager::getInstance(SqlFile::class);
                $context->getPrinter()->printList($sqlFile->import_data($sql_file, 'bbs_'));
                $context->getPrinter()->setup('数据库Sql文件导入完成.');
            }
        }

    }
}
