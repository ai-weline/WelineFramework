<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\Model;


use Weline\Framework\Database\Api\Db\Ddl\Table\CreateInterface;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Database\Db\Ddl\Create;
use Weline\Framework\Database\Model;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Db\Setup;

class Index extends Model
{

    function provideTable(): string
    {
        return '';
    }

    function providePrimaryField(): string
    {
        return 'id';
    }

    function setup(): void
    {
        /**@var Setup $setup */
        $setup = ObjectManager::getInstance(Setup::class);
        # 有表则删除
        if ($setup->tableExist($this->getTable())) {
             $setup->dropTable($this->getTable());
        }
        # 创建表
        $setup->createTable($this->getTable(),'测试表')
            ->addColumn('id', TableInterface::column_type_INTEGER, 11, 'primary key', 'ID')
            ->addColumn('i', TableInterface::column_type_VARCHAR, 11, 'default 123', 'i')
            ->addColumn('ii', TableInterface::column_type_VARCHAR, 11, 'default 111', 'i')
            ->create();


        # TODO 完成自动改表
        $setup->alterTable($this->getTable(), '测试修改')
            ->deleteColumn('i')
            ->addColumn('name', TableInterface::column_type_TEXT, 12, '', '名称')
            ->addColumn('type', TableInterface::column_type_TEXT, 12, '', '类型')
            ->addColumn('ii', TableInterface::column_type_TEXT, 12, '', 'ii')
            ->alterColumn('id', 'ii_test','id')
            ->alter();
    }
}