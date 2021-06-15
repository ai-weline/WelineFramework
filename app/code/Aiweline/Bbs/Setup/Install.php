<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Setup;

use Weline\Framework\Database\Helper\Importer\SqlFile;
use Weline\Framework\Setup\Data;

class Install implements \Weline\Framework\Setup\InstallInterface
{
    /**
     * @var SqlFile
     */
    private SqlFile $sqlFile;

    public function __construct(
        SqlFile $sqlFile
    ) {
        $this->sqlFile = $sqlFile;
    }

    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $sql_file_path = $context->getModulePath() . DIRECTORY_SEPARATOR . 'Setup' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'db_bbs_20201129_144653.sql';
        if (@is_file($sql_file_path)) {
            $context->getPrinter()->setup('数据库Sql文件导入中...');
            $context->getPrinter()->printList($this->sqlFile->import_data($sql_file_path, 'bbs_'));
            $context->getPrinter()->setup('数据库Sql文件导入完成.');
        }
    }
}
