<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/12 21:15:14
 */

namespace Weline\Acl\Model;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class RoleAccess extends \Weline\Framework\Database\Model
{

    public const fields_ID      = 'role_id';
    public const fields_ROLE_ID = Role::fields_ID;
    public const fields_ACL_ID  = Acl::fields_ACL_ID;

    private array $exist = [];

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable()
                  ->addColumn(
                      self::fields_ROLE_ID,
                      TableInterface::column_type_INTEGER,
                      null,
                      'not null',
                      '角色ID'
                  )
                  ->addColumn(
                      self::fields_ACL_ID,
                      TableInterface::column_type_INTEGER,
                      null,
                      'not null',
                      '资源ID'
                  )
                  ->addForeignKey(
                      'ROLE_ACCESS_ROLE_ID',
                      self::fields_ROLE_ID,
                      $this->getTable('role'),
                      Role::fields_ID,
                      true
                  )
                  ->addConstraints("primary key (role_id,acl_id)")
                /*
                  ->addForeignKey(
                      'ROLE_ACCESS_ID',
                      self::fields_ACL_ID,
                      $this->getTable('acl'),
                      Acl::fields_ACL_ID,
                      true
                  )*/
                  ->create();
        }
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/1/19 22:16
     * 参数区：
     *
     * @param string     $main_field
     * @param string     $parent_id_field
     * @param string|int $parent_id_value
     * @param string     $order_field
     * @param string     $order_sort
     *
     * @return \Weline\Acl\Model\RoleAccess[]
     * @throws null
     */
    public function getTree(
        string     $main_field = '',
        string     $parent_id_field = 'parent_id',
        string|int $parent_id_value = 0,
        string     $order_field = 'position',
        string     $order_sort = 'ASC'
    ): array
    {
        /**@var \Weline\Acl\Model\Role $roleModel */
        $roleModel = ObjectManager::getInstance(Role::class);
        $this->getEvenManager()->dispatch('Weline_Acl::check_role', $roleModel);
        // 超管
        if ($roleModel->getId() === 1) {
            /**@var \Weline\Acl\Model\Acl $aclModel */
            $aclModel = ObjectManager::getInstance(\Weline\Acl\Model\Acl::class);
            return $aclModel->getTree(
                $aclModel::fields_SOURCE_ID,
                $aclModel::fields_PARENT_SOURCE,
                '',
                $aclModel::fields_ACL_ID
            );
        } else {
            if (empty($roleModel->getId())) {
                return [];
            }
            return $this->getRoleAccessTree($roleModel);
        }
    }

    public function getRoleAccessList(Role $roleModel): array
    {
        return $this->joinModel($roleModel, 'r', 'main_table.role_id=r.role_id')
                    ->joinModel(Acl::class, 'a', 'main_table.acl_id=a.acl_id')
                    ->where('main_table.role_id', $roleModel->getId())
                    ->select()
                    ->fetch()
                    ->getItems();
    }

    public function getRoleNotAccessList(Role $roleModel): array
    {
        /**@var Acl $acl */
        $acl = ObjectManager::getInstance(Acl::class);
        return $acl
            ->where("acl_id not in (select acl_id from {$this->getTable()} where {$this->getTable()}.role_id='{$roleModel->getId()}')")
            ->select()
            ->fetchOrigin();
    }

    public function getRoleAccessTree(\Weline\Acl\Model\Role &$roleModel): array
    {
        // 顶层 TODO 角色权限树读取问题
        /**@var \Weline\Acl\Model\RoleAccess[] $trees */
        $trees = $this->joinModel($roleModel, 'r', 'main_table.role_id=r.role_id')
                      ->joinModel(Acl::class, 'a', 'main_table.acl_id=a.acl_id')
                      ->where('main_table.role_id', $roleModel->getId())
                      ->where('a.parent_source', '')
                      ->select()
                      ->fetch()
                      ->getItems();
        $this->exist = [];
        foreach ($trees as &$tree) {
            $this->exist[] = $tree->getData('acl_id');
            $tree = $tree->getRoleAccessSubs(
                $tree,
                $roleModel,
                Acl::fields_SOURCE_ID,
                Acl::fields_PARENT_SOURCE,
                Acl::fields_ACL_ID,
                'ASC'
            );
        }
        return $trees;
    }

    /**
     * @DESC          # 获取角色权限树
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/1/19 21:59
     * 参数区：
     *
     * @param \Weline\Acl\Model\RoleAccess $model
     * @param \Weline\Acl\Model\Role       $roleModel
     * @param string                       $main_field
     * @param string                       $parent_id_field
     * @param string                       $order_field
     * @param string                       $order_sort
     *
     * @return \Weline\Acl\Model\RoleAccess
     */
    function getRoleAccessSubs(
        RoleAccess &$model,
        Role       &$roleModel,
        string     $main_field = '',
        string     $parent_id_field = 'parent_id',
        string     $order_field = 'position',
        string     $order_sort = 'ASC'
    ): RoleAccess
    {
        $main_field = $main_field ?: $this::fields_ID;
        if ($subs = $this->clear()
                         ->joinModel($roleModel, 'r', 'main_table.role_id=r.role_id')
                         ->joinModel(Acl::class, 'a', 'main_table.acl_id=a.acl_id')
                         ->where($parent_id_field, $model->getData($main_field) ?: '')
                         ->where('main_table.' . Role::fields_ROLE_ID, $roleModel->getId(0))
                         ->order($order_field, $order_sort)
                         ->select()
                         ->fetch()
                         ->getItems()
        ) {
            foreach ($subs as &$sub) {
                if(!in_array($sub->getData('acl_id'), $this->exist)){
                    $this->exist[] = $sub->getData('acl_id');
                    $sub = $this->getRoleAccessSubs($sub, $roleModel, $main_field, $parent_id_field, $order_field, $order_sort);
                }
            }
            $model = $model->setData('sub', $subs);
        } else {
            $model = $model->setData('sub', []);
        }
        return $model;
    }
}