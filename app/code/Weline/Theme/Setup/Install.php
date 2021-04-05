<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Setup;

use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Setup\Data;
use Weline\Framework\Setup\InstallInterface;
use Weline\Theme\Model\Theme;

class Install implements InstallInterface
{
    const table_THEME = 'weline_theme';
    /**
     * @var Theme
     */
    private Theme $theme;

    function __construct(
        Theme $theme
    )
    {
        $this->theme = $theme;
    }

    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $printer = $setup->getPrinter();
        $printer->note('安装开始...');
        $db = $setup->getDb();
        /*主题表*/
        $printer->warning('安装数据库表：' . self::table_THEME);
        if (! $db->tableExist(self::table_THEME)) {
            $setup->getDb()->createTable(
                self::table_THEME,
                '主题表'
            )->addColumn(
                'id',
                Table::column_type_INTEGER,
                11,
                'primary key NOT NULL AUTO_INCREMENT',
                'ID'
            )->addColumn(
                'name',
                Table::column_type_VARCHAR,
                '60',
                'NOT NULL',
                '主题名'
            )->addColumn(
                'parent_id',
                Table::column_type_INTEGER,
                11,
                '',
                '父级主题'
            )->addColumn(
                'is_active',
                Table::column_type_INTEGER,
                11,
                '',
                '是否激活'
            )->addColumn(
                'create_time',
                Table::column_type_DATETIME,
                null,
                'NOT NULL DEFAULT CURRENT_TIMESTAMP',
                '安装时间'
            )->addIndex(
                Table::index_type_FULLTEXT,
                'name',
                'name'
            )->addIndex(
                Table::index_type_DEFAULT,
                'parent_id',
                'parent_id'
            )->create();
        }
        $printer->warning('正在写入默认主题...');
        $this->theme->
        $printer->note('安装结束...');
    }
}
