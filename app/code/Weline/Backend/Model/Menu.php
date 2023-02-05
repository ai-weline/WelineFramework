<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Model;

use Weline\Acl\Model\Acl;
use Weline\Acl\Model\Role;
use Weline\Acl\Model\RoleAccess;
use Weline\Backend\Cache\BackendCache;
use Weline\Framework\App\Env;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Http\Url;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Menu extends \Weline\Framework\Database\Model
{
    public const primary_key = 'name';

    public const fields_NAME          = 'name';
    public const fields_TITLE         = 'title';
    public const fields_PID           = 'pid';
    public const fields_SOURCE        = 'source';
    public const fields_PARENT_SOURCE = 'parent_source';
    public const fields_ACTION        = 'action';
    public const fields_MODULE        = 'module';
    public const fields_ICON          = 'icon';
    public const fields_ORDER         = 'order';
    public const fields_IS_SYSTEM     = 'is_system';
    public const fields_IS_ENABLE     = 'is_enable';
    public const fields_IS_BACKEND    = 'is_backend';

    private Url $url;

    public function __init()
    {
        parent::__init();
        if (!isset($this->url)) {
            $this->url = ObjectManager::getInstance(Url::class);
        }
    }

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
        $setup->getPrinting()->setup('安装数据表...' . self::table);
        if (!$setup->tableExist()) {
            $setup->createTable('后端菜单表')
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, null, 'primary key auto_increment', 'ID')
                  ->addColumn(self::fields_NAME, TableInterface::column_type_VARCHAR, 60, 'not null', '菜单名')
                  ->addColumn(self::fields_TITLE, TableInterface::column_type_VARCHAR, 60, 'not null', '菜单标题')
                  ->addColumn(self::fields_PID, TableInterface::column_type_INTEGER, 0, '', '父级ID')
                  ->addColumn(self::fields_SOURCE, TableInterface::column_type_VARCHAR, 255, '', '资源')
                  ->addColumn(self::fields_PARENT_SOURCE, TableInterface::column_type_VARCHAR, 255, 'not null', '父级资源')
                  ->addColumn(self::fields_ACTION, TableInterface::column_type_VARCHAR, 255, 'not null', '动作URL')
                  ->addColumn(self::fields_MODULE, TableInterface::column_type_VARCHAR, 255, 'not null', '模块')
                  ->addColumn(self::fields_ICON, TableInterface::column_type_VARCHAR, 60, 'not null', 'Icon图标类')
                  ->addColumn(self::fields_ORDER, TableInterface::column_type_INTEGER, null, 'not null', '排序')
                  ->addColumn(self::fields_IS_BACKEND, TableInterface::column_type_INTEGER, 1, 'default 1', '是否后台菜单')
                  ->addColumn(self::fields_IS_SYSTEM, TableInterface::column_type_INTEGER, 1, 'default 0', '是否系统菜单')
                  ->addColumn(self::fields_IS_ENABLE, TableInterface::column_type_INTEGER, 1, 'default 1', '是否启用')
                  ->addAdditional('ENGINE=MyIsam;')
                  ->create();
        } else {
            $setup->getPrinting()->warning('数据表存在，跳过安装数据表...' . self::table);
        }
    }

    public function getName(): string
    {
        return parent::getData(self::fields_NAME) ?? '';
    }

    public function setName(string $name): static
    {
        return parent::setData(self::fields_NAME, $name);
    }

    public function getPid()
    {
        return parent::getData(self::fields_PID);
    }

    public function setPid(string $pid): static
    {
        return parent::setData(self::fields_NAME, $pid);
    }

    public function getSource(): string
    {
        return parent::getData(self::fields_SOURCE) ?? '';
    }

    public function setSource(string $source): static
    {
        return parent::setData(self::fields_SOURCE, $source);
    }

    public function getParentSource(): string
    {
        return parent::getData(self::fields_PARENT_SOURCE) ?? '';
    }

    public function setParentSource(string $source): static
    {
        return parent::setData(self::fields_PARENT_SOURCE, $source);
    }

    public function getAction(): string
    {
        return parent::getData(self::fields_ACTION) ?? '';
    }

    public function setAction(string $url): static
    {
        return parent::setData(self::fields_ACTION, $url);
    }

    public function getIcon(): string
    {
        return parent::getData(self::fields_ICON) ?? '';
    }

    public function setIcon(string $css_icon_class): static
    {
        return parent::setData(self::fields_ICON, $css_icon_class);
    }

    public function getTitle(): string
    {
        return parent::getData(self::fields_TITLE) ?? '';
    }

    public function setTitle(string $title): static
    {
        return parent::setData(self::fields_ICON, $title);
    }

    public function getOrder(): int
    {
        return intval(parent::getData(self::fields_ORDER));
    }

    public function setOrder(int $order): static
    {
        return parent::setData(self::fields_ORDER, $order);
    }

    public function getModule(): string
    {
        return $this->getData(self::fields_MODULE) ?? '';
    }

    public function setModule(string $module_name): static
    {
        return $this->setData(self::fields_MODULE, $module_name);
    }

    public function isSystem(): bool
    {
        return (bool)$this->getData(self::fields_IS_SYSTEM);
    }

    public function setIsSystem(bool $is_system): static
    {
        return $this->setData(self::fields_IS_SYSTEM, $is_system);
    }

    public function isEnable(): bool
    {
        return (bool)$this->getData(self::fields_IS_ENABLE);
    }

    public function setIsEnable(bool $is_enable): static
    {
        return $this->setData(self::fields_IS_ENABLE, $is_enable);
    }

    public function isBackend(): bool
    {
        return (bool)$this->getData(self::fields_IS_BACKEND);
    }

    public function setIsBackend(bool $is_backend): static
    {
        return $this->setData(self::fields_IS_BACKEND, $is_backend);
    }

    /*----------------------助手函数区-------------------------*/
    public function getUrl(): string
    {
        if (!$this->isBackend()) {
            $url = '/' . trim($this->getAction(), '/');
        } else {
            $url = $this->url->getBackendUrl('/'.trim($this->getAction(), '/'));
        }
        return $url ?? '';
    }

    /**
     * @DESC          # 获取菜单树
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/9 0:38
     * 参数区：
     */
    public function getMenuTree(): mixed
    {
        $top_menus = $this->clear()->where($this::fields_PID, 0)->order('order', 'ASC')->select()->fetch()->getItems();
        /**@var \Weline\Backend\Model\Menu $top_menu */
        foreach ($top_menus as &$top_menu) {
            $top_menu = $this->getSubMenus($top_menu);
        }
        return $top_menus;
    }

    public function getSubMenus(\Weline\Backend\Model\Menu &$menu): Menu
    {
        if ($sub_menus = $this->clear()
                              ->where($this::fields_PID, $menu->getData('id'))
                              ->order('order', 'ASC')
                              ->select()
                              ->fetch()
                              ->getItems()
        ) {
            /**@var \Weline\Backend\Model\Menu $sub_menu */
            foreach ($sub_menus as &$sub_menu) {
                $has_sub_menu = $this
                    ->clear()
                    ->where($this::fields_PID, $sub_menu->getData('id'))
                    ->order('order', 'ASC')
                    ->find()
                    ->fetch();
                if ($has_sub_menu->getData('id')) {
                    $sub_menu = $this->getSubMenus($sub_menu);
                }
            }
            $menu = $menu->setData('sub_menu', $sub_menus);
        } else {
            $menu = $menu->setData('sub_menu', []);
        }
        return $menu;
    }

    /**
     * @DESC          # 获取角色菜单树
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/9 0:38
     * 参数区：
     */
    public function getMenuTreeByRole(Role $role): array
    {
        if ($role->getId() !== 1) {
            // 以子权限扫描所有权限的父级
            $roleAccesses = $this->clear()
                                 ->joinModel(Acl::class, 'a', 'main_table.source=a.source_id')
                                 ->joinModel(RoleAccess::class, 'ra', 'ra.acl_id=a.acl_id')
                                 ->where('ra.' . RoleAccess::fields_ROLE_ID, $role->getId(0))
                                 ->where('main_table.pid', 0, '<>')
                                 ->select()
                                 ->fetchOrigin();
            $hasIds       = [];
            $top_menus    = [];
            // 归并所有相同父级的权限,同时筛选出父级权限资源递归出子权限
            $mergerParentAcl = [];
            foreach ($roleAccesses as $roleAccess) {
                $parentSource = $roleAccess['parent_source'];
                // 顶层资源,找出对应是否有权限的子权限
                if (empty($parentSource)) {
                    $top_menus[] = $this->getSubMenusByRole($roleAccess, $role);
                } else {
                    // 归并需要查找父级的子权限
                    $mergerParentAcl[$parentSource][] = $roleAccess;
                }
            }

            foreach ($mergerParentAcl as $parentSource => $acls) {
                $menu     = $this->clear()->load('source', $parentSource);
                $menu->setData('sub_menu_by_role',$acls);
                $top_menu = $this->findTopMenu($menu);
                if (!in_array($top_menu->getData('id'), $hasIds)) {
                    $top_menus[] = $top_menu;
                    $hasIds[]    = $top_menu->getData('id');
                }
            }
        } else {
            $top_menus = $this->clear()
                              ->joinModel(Acl::class, 'a', 'main_table.source=a.source_id')
                              ->joinModel(RoleAccess::class, 'ra', 'ra.acl_id=a.acl_id')
                              ->where('main_table.pid', 0)
                              ->select()
                              ->fetch()
                              ->getItems();
        }

        /**@var \Weline\Backend\Model\Menu $top_menu */
        foreach ($top_menus as &$top_menu) {
            $top_menu = $this->getSubMenusByRole($top_menu, $role);
        }
        return $top_menus;
    }

    /**
     * @DESC          # 查找顶层菜单
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2023/1/31 23:25
     * 参数区：
     *
     * @param \Weline\Backend\Model\Menu $menu
     *
     * @return \Weline\Backend\Model\Menu
     * @throws \ReflectionException
     * @throws \Weline\Framework\Exception\Core
     */
    private function findTopMenu(Menu $menu): Menu
    {
        $menuData = clone $menu;
        if ($menuData->getPid() === 0) {
            return $menuData;
        } else {
            $parent = $this->clear()->load('id', $menuData->getPid());
            $parent->setData('sub_menu_by_role',[$menuData]);
            return $this->findTopMenu($parent);
        }
    }

    /**
     * @DESC          # 获取角色权限子菜单
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/20 23:18
     * 参数区：
     * @return \Weline\Backend\Model\Menu[]
     */
    public function getSubMenuByRole(): array
    {
        return $this->getData('sub_menu_by_role') ?? [];
    }

    public function getSubMenusByRole(\Weline\Backend\Model\Menu &$menu, Role $role): Menu
    {
        $this->clear()
             ->joinModel(Acl::class, 'a', 'main_table.source=a.source_id')
             ->joinModel(RoleAccess::class, 'ra', 'ra.acl_id=a.acl_id')
             ->where('main_table.pid', $menu->getData('id'));
        if ($role->getId() !== 1) {
            $this->where('ra.role_id', $role->getId());
        }
        // 有权限的
        $has_ids = [];
        if ($sub_menus = $this->order('order', 'ASC')->select()->fetch()->getItems()) {
            /**@var \Weline\Backend\Model\Menu $sub_menu */
            foreach ($sub_menus as &$sub_menu) {
                $has_ids[] = $sub_menu->getData('id');
                $this->clear()
                     ->joinModel(Acl::class, 'a', 'main_table.source=a.source_id')
                     ->joinModel(RoleAccess::class, 'ra', 'ra.acl_id=a.acl_id')
                     ->where($this::fields_PID, $sub_menu->getData('id'));
                if ($role->getId() !== 1) {
                    $this->where('ra.role_id', $role->getId());
                }
                $has_sub_menus = $this
                    ->order('order', 'ASC')
                    ->select()
                    ->fetch()->getItems();
                foreach ($has_sub_menus as $has_sub_menu) {
                    if ($has_sub_menu->getData('id')) {
                        $sub_menu = $this->getSubMenusByRole($sub_menu, $role);
                    }
                }
            }
            $menu = $menu->setData('sub_menu_by_role', $sub_menus);
        } else {
            $menu = $menu->setData('sub_menu_by_role', []);
        }

        // TODO 再检测没有权限的子菜单,都没有权限才给空$menu = $menu->setData('sub_menu_by_role', []);
//        {
//            $has_ids = implode(',', $has_ids);
//            $subs    = $this->clear()
//                            ->joinModel(Acl::class, 'a', 'main_table.source=a.source_id')
//                            ->joinModel(RoleAccess::class, 'ra', 'ra.acl_id=a.acl_id')
//                            ->where("main_table.id not in ({$has_ids})")
//                            ->where('main_table.pid', $menu->getData('id'))
//                            ->select()
//                            ->fetch()
//                            ->getItems();
//            d($subs);
//            if ($subs) {
//                /**@var \Weline\Backend\Model\Menu $sub */
//                foreach ($subs as $key => &$sub) {
//                    $has = $this->getSubMenusByRole($sub, $role);
//                    if (!$has->getSubMenuByRole()) {
//                        unset($subs[$key]);
//                    }
//                }
//                $menu = $menu->setData('sub_menu_by_role', $subs);
//            } else {
//                $menu = $menu->setData('sub_menu_by_role', []);
//            }
//        }
        return $menu;
    }
}
