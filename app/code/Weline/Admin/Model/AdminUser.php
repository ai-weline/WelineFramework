<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Model;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class AdminUser extends \Weline\Framework\Database\Model
{

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        /*$setup->createTable('管理员表')
            ->addColumn('user_id', TableInterface::column_type_INTEGER, 0, 'primary key', '用户ID')
            ->addColumn('username', TableInterface::column_type_VARCHAR, 60, '', '用户名')
            ->addColumn('password', TableInterface::column_type_VARCHAR, 255, '', '密码')
            ->create();*/
    }

    /**
     * @inheritDoc
     */
    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    function install(ModelSetup $setup, Context $context): void
    {
        $setup->createTable('管理员表')
            ->addColumn('user_id', TableInterface::column_type_INTEGER, 0, 'primary key', '用户ID')
            ->addColumn('username', TableInterface::column_type_VARCHAR, 60, '', '用户名')
            ->addColumn('password', TableInterface::column_type_VARCHAR, 255, '', '密码')
            ->create();
    }

    function providePrimaryField(): string
    {
        return 'user_id';
    }
}