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
use Weline\Framework\Setup\Db\ModelSetup;
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

    /**
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    function setup(ModelSetup $setup): void
    {
        # 有表则删除
        if ($setup->tableExist()) {
            $setup->dropTable();
        }
        # 创建表
//        $createResult = $setup->createTable('测试表')
//            ->addColumn('id', TableInterface::column_type_INTEGER, 11, 'unsigned primary key auto_increment', 'ID')
//            ->addColumn('i', TableInterface::column_type_VARCHAR, 11, 'default 123', 'i')
//            ->addColumn('ii', TableInterface::column_type_INTEGER, 11, 'unsigned default 111', 'i')
//            ->create()->fetch();

//        # TODO 完成自动改表
//        $setup->alterTable('测试修改','ii')
//            ->deleteColumn('i')
//            ->addColumn('name', TableInterface::column_type_TEXT, 12, '', '名称')
//            ->addColumn('type', TableInterface::column_type_TEXT, 12, '', '类型')
//            ->alterColumn('ii', 'ii_test','id',TableInterface::column_type_VARCHAR,11,"default '111'",'reiurhrhhsadifha')
//            ->alterColumn('i', 'i','id',TableInterface::column_type_VARCHAR,11,"default '111'",'ddddd')
//            ->alter();
    }
}