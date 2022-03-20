<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Model;

use Weline\Backend\Model\Config;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\View\Template;

class AdminUser extends \Weline\Framework\Database\Model
{

    const fields_ID = 'user_id';
    const fields_username = 'username';
    const fields_password = 'password';
    const fields_avatar = 'avatar';
    const fields_login_ip = 'login_ip';
    const fields_attempt_ip = 'attempt_ip';
    const fields_attempt_times = 'attempt_times';
    const fields_sess_id = 'sess_id';

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
//        $setup->createTable('管理员表')
//            ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'auto_increment primary key', '用户ID')
//            ->addColumn(self::fields_username, TableInterface::column_type_VARCHAR, 60, '', '用户名')
//            ->addColumn(self::fields_password, TableInterface::column_type_VARCHAR, 255, '', '密码')
//            ->addColumn(self::fields_avatar, TableInterface::column_type_VARCHAR, 255, '', '头像')
//            ->addColumn(self::fields_login_ip, TableInterface::column_type_VARCHAR, 16, '', '登录IP')
//            ->addColumn(self::fields_sess_id, TableInterface::column_type_VARCHAR, 32, '', '管理员Session ID')
//            ->addColumn(self::fields_attempt_times, TableInterface::column_type_INTEGER, 0, '', '尝试登录次数')
//            ->addColumn(self::fields_attempt_ip, TableInterface::column_type_VARCHAR, 16, '', '尝试登录IP')
//            ->create();
//
//        # 初始化一个账户
//        /**@var AdminUser $adminUser */
//        $adminUser = ObjectManager::getInstance(AdminUser::class);
//        $adminUser->setUsername('Admin')->setPassword('admin')->save();
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
        if (!$setup->tableExist()) {
            $setup->createTable('管理员表')
                ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'auto_increment primary key', '用户ID')
                ->addColumn(self::fields_username, TableInterface::column_type_VARCHAR, 60, '', '用户名')
                ->addColumn(self::fields_password, TableInterface::column_type_VARCHAR, 255, '', '密码')
                ->addColumn(self::fields_avatar, TableInterface::column_type_VARCHAR, 255, '', '头像')
                ->addColumn(self::fields_login_ip, TableInterface::column_type_VARCHAR, 16, '', '登录IP')
                ->addColumn(self::fields_sess_id, TableInterface::column_type_VARCHAR, 32, '', '管理员Session ID')
                ->addColumn(self::fields_attempt_times, TableInterface::column_type_INTEGER, 0, '', '尝试登录次数')
                ->addColumn(self::fields_attempt_ip, TableInterface::column_type_VARCHAR, 16, '', '尝试登录IP')
                ->create();

            # 初始化一个账户
            /**@var AdminUser $adminUser */
            $adminUser = ObjectManager::getInstance(AdminUser::class);
            $adminUser->setUsername('Admin')->setPassword('admin')->save();
        }
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

    function getAttemptIp()
    {
        return $this->getData(self::fields_attempt_ip);
    }

    function setAttemptIp($ip)
    {
        return $this->setData(self::fields_attempt_ip, $ip);
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

    function setUsername(string $username)
    {
        return $this->setData('username', $username);
    }

    function getAvatar()
    {
        return $this->getData('avatar');
    }

    function setAvatar(string $avatar)
    {
        return $this->setData('avatar', $avatar);
    }

    function getPassword()
    {
        return $this->getData('password');
    }

    function setPassword(string $password)
    {
        return $this->setData('password', password_hash($password, PASSWORD_DEFAULT));
    }


    function getSessionId()
    {
        return $this->getData(self::fields_sess_id);
    }

    function setSessionId(string $sess_id): AdminUser
    {
        return $this->setData(self::fields_sess_id, $sess_id);
    }

    function getLoginIp()
    {
        return $this->getData(self::fields_login_ip);
    }

    function setLoginIp(string $ip): AdminUser
    {
        return $this->setData(self::fields_login_ip, $ip);
    }
}