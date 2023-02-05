<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Model;

use Weline\Acl\Model\Role;
use Weline\Backend\Model\Backend\Acl\UserRole;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class BackendUser extends \Weline\Framework\Database\Model
{
    public const fields_ID            = 'user_id';
    public const fields_email         = 'email';
    public const fields_username      = 'username';
    public const fields_password      = 'password';
    public const fields_avatar        = 'avatar';
    public const fields_login_ip      = 'login_ip';
    public const fields_attempt_ip    = 'attempt_ip';
    public const fields_attempt_times = 'attempt_times';
    public const fields_sess_id       = 'sess_id';

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
        /*$setup->alterTable()
              ->addColumn(self::fields_email, 'user_id', TableInterface::column_type_VARCHAR, 255, 'not null unique', '邮箱')
              ->alter();*/
//        $setup->forceDropTable();
        if (!$setup->tableExist()) {
            $setup->createTable('管理员表')
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'auto_increment primary key', '用户ID')
                  ->addColumn(self::fields_email, TableInterface::column_type_VARCHAR, 255, 'not null unique', '邮箱')
                  ->addColumn(self::fields_username, TableInterface::column_type_VARCHAR, 60, '', '用户名')
                  ->addColumn(self::fields_password, TableInterface::column_type_VARCHAR, 255, '', '密码')
                  ->addColumn(self::fields_avatar, TableInterface::column_type_VARCHAR, 255, '', '头像')
                  ->addColumn(self::fields_login_ip, TableInterface::column_type_VARCHAR, 16, '', '登录IP')
                  ->addColumn(self::fields_sess_id, TableInterface::column_type_VARCHAR, 32, '', '管理员Session ID')
                  ->addColumn(self::fields_attempt_times, TableInterface::column_type_INTEGER, 0, 'default 0', '尝试登录次数')
                  ->addColumn(self::fields_attempt_ip, TableInterface::column_type_VARCHAR, 16, '', '尝试登录IP')
                  ->addAdditional('ENGINE=MyIsam;')
                  ->create();

            # 初始化一个账户
            $this->setUsername('秋枫雁飞')
                 ->setEmail('admin@weline.com')
                 ->setPassword('admin')
                 ->save();
        }
    }

    public function getAttemptTimes(): int
    {
        return intval($this->getData(self::fields_attempt_times));
    }

    public function addAttemptTimes(): static
    {
        $this->setData(self::fields_attempt_times, intval($this->getData(self::fields_attempt_times)) + 1);
        return $this;
    }

    public function getAttemptIp()
    {
        return $this->getData(self::fields_attempt_ip);
    }

    public function setAttemptIp($ip): BackendUser
    {
        return $this->setData(self::fields_attempt_ip, $ip);
    }

    public function resetAttemptTimes(): static
    {
        $this->setData(self::fields_attempt_times, 0);
        $this->save();
        return $this;
    }

    public function getUsername()
    {
        return $this->getData('username');
    }

    public function setUsername(string $username): BackendUser
    {
        return $this->setData('username', $username);
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function setEmail(string $email): BackendUser
    {
        return $this->setData('email', $email);
    }

    public function getAvatar()
    {
        return $this->getData('avatar');
    }

    public function setAvatar(string $avatar): BackendUser
    {
        return $this->setData('avatar', $avatar);
    }

    public function getPassword()
    {
        return $this->getData('password');
    }

    public function setPassword(string $password): BackendUser
    {
        return $this->setData('password', password_hash($password, PASSWORD_DEFAULT));
    }


    public function getSessionId()
    {
        return $this->getData(self::fields_sess_id);
    }

    public function setSessionId(string $sess_id): BackendUser
    {
        return $this->setData(self::fields_sess_id, $sess_id);
    }

    public function getLoginIp()
    {
        return $this->getData(self::fields_login_ip);
    }

    public function setLoginIp(string $ip): BackendUser
    {
        return $this->setData(self::fields_login_ip, $ip);
    }

    public function getRole(): Backend\Acl\UserRole
    {
        if ($role = $this->getData('user_role')) {
            return $role;
        }
        /**@var \Weline\Backend\Model\Backend\Acl\UserRole $userRole */
        $userRole = ObjectManager::getInstance(UserRole::class);
        $userRole->joinModel(Role::class, 'r', 'main_table.role_id=r.role_id')
                 ->where('main_table.' . self::fields_ID, $this->getId())
                 ->find()->fetch();
        $this->setData('user_role', $userRole);
        return $userRole;
    }

    public function getRoleModel(): Role
    {
        if ($role = $this->getData('role')) {
            return $role;
        }
        /**@var Role $role */
        $role = ObjectManager::getInstance(Role::class);
        $role = $role->load($this->getRole()->getRoleId() ?? '');
        if ($role->getId()) $this->setData('role', $role);
        return $role;
    }

    public function assignRole(int $role_id)
    {
        /**@var UserRole $userRole */
        $userRole = ObjectManager::getInstance(UserRole::class);
        $userRole->setUserId($this->getId())
                 ->setRoleId($role_id)
                 ->save(true);
    }
}
