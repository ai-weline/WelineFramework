<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Model;

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
    public const table       = 'm_backend_menu';
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
        // TODO: Implement upgrade() method.
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
            $url = $this->url->build(trim($this->getAction(), '/'));
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
        $top_menus = $this->clearData()->where($this::fields_PID, 0)->order('order', 'ASC')->select()->fetch()->getItems();
        foreach ($top_menus as &$top_menu) {
            $top_menu = $this->getSubMenus($top_menu);
        }
        return $top_menus;
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/20 23:18
     * 参数区：
     * @return \Weline\Backend\Model\Menu[]
     */
    public function getSubMenu(): array
    {
        return $this->getData('sub_menu') ?? [];
    }

    public function getSubMenus(\Weline\Backend\Model\Menu &$menu): Menu
    {
        if ($sub_menus = $this->clearData()->where($this::fields_PID, $menu->getData('id'))->order('order', 'ASC')->select()->fetch()->getItems()) {
            foreach ($sub_menus as &$sub_menu) {
                $has_sub_menu = $this->clearData()->where($this::fields_PID, $sub_menu->getData('id'))->find()->fetch();
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
}
