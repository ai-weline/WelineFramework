<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Model;

use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Database\Model;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class WelineMenu extends Model
{

    function setup(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement setup() method.
    }

    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    function install(ModelSetup $setup, Context $context): void
    {
        if($setup->tableExist()){
            $setup->createTable()
                ->addColumn('id', TableInterface::column_type_INTEGER, 11, 'not null primary key unsigned auto_increment', '菜单ID')
                ->addColumn('p_id', TableInterface::column_type_INTEGER, 11, 'unsigned', '父级ID')
                ->addColumn('name', TableInterface::column_type_VARCHAR, 20, 'not null ', '菜单')
                ->addColumn('url', TableInterface::column_type_VARCHAR, 255, 'not null ', 'URL')
                ->addColumn('module', TableInterface::column_type_VARCHAR, 60, 'not null ', '模组名')
                ->create();
        }

    }
}