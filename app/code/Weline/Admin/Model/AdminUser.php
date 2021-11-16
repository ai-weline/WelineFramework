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
    
    const fields_ID = 'user_id';
    const fields_attempt_times = 'attempt_times';
    const fields_username = 'username';
    const fields_password = 'password';

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        /*$setup->dropTable();
        $setup->createTable('管理员表')
            ->addColumn('user_id', TableInterface::column_type_INTEGER, 0, 'auto_increment primary key', '用户ID')
            ->addColumn('username', TableInterface::column_type_VARCHAR, 60, '', '用户名')
            ->addColumn('password', TableInterface::column_type_VARCHAR, 255, '', '密码')
            ->addColumn(self::fields_attempt_times, TableInterface::column_type_SMALLINT, 1, '', '尝试登录次数')
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

    function getAttemptTimes()
    {
        return intval($this->getData(self::fields_attempt_times));
    }

    function addAttemptTimes(): static
    {
        $this->setData(self::fields_attempt_times, intval($this->getData(self::fields_attempt_times)) + 1);
        return $this;
    }

    function resetAttemptTimes(): static
    {
        $this->setData(self::fields_attempt_times, 0);
        $this->save();
        return $this;
    }

    function getUsername()
    {
        return $this->getData('username');
    }

    function getPassword()
    {
        return $this->getData('password');
    }
}