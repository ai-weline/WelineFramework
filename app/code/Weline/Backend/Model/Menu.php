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
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Http\Url;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Menu extends \Weline\Framework\Database\Model
{
    const table = 'm_backend_menu';

    const fields_NAME = 'name';
    const fields_TITLE = 'title';
    const fields_PID = 'pid';
    const fields_SOURCE = 'source';
    const fields_PARENT_SOURCE = 'parent_source';
    const fields_ACTION = 'action';
    const fields_MODULE = 'module';

    private CacheInterface $backendCache;

    private Url $url;

    function __construct(
        Url   $url,
        BackendCache $backendCache,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->url = $url;
        $this->backendCache = $backendCache->create();
    }

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        /*$setup->dropTable();
        $setup->getPrinting()->setup('安装数据表...' . self::table);
        $setup->createTable('后端菜单表')
            ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment', 'ID')
            ->addColumn(self::fields_NAME, TableInterface::column_type_VARCHAR, 60, 'not null', '菜单名')
            ->addColumn(self::fields_TITLE, TableInterface::column_type_VARCHAR, 60, 'not null', '菜单标题')
            ->addColumn(self::fields_PID, TableInterface::column_type_INTEGER, 0, '', '父级ID')
            ->addColumn(self::fields_SOURCE, TableInterface::column_type_VARCHAR, 255, '', '资源')
            ->addColumn(self::fields_PARENT_SOURCE, TableInterface::column_type_VARCHAR, 255, 'not null', '父级资源')
            ->addColumn(self::fields_ACTION, TableInterface::column_type_VARCHAR, 255, 'not null', '动作URL')
            ->addColumn(self::fields_MODULE, TableInterface::column_type_VARCHAR, 255, 'not null', '模块')
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
        $setup->getPrinting()->setup('安装数据表...' . self::table);
        if (!$setup->tableExist()) {
            $setup->createTable('后端菜单表')
                ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment', 'ID')
                ->addColumn(self::fields_NAME, TableInterface::column_type_VARCHAR, 60, 'not null', '菜单名')
                ->addColumn(self::fields_TITLE, TableInterface::column_type_VARCHAR, 60, 'not null', '菜单标题')
                ->addColumn(self::fields_PID, TableInterface::column_type_INTEGER, 0, '', '父级ID')
                ->addColumn(self::fields_SOURCE, TableInterface::column_type_VARCHAR, 255, '', '资源')
                ->addColumn(self::fields_PARENT_SOURCE, TableInterface::column_type_VARCHAR, 255, 'not null', '父级资源')
                ->addColumn(self::fields_ACTION, TableInterface::column_type_VARCHAR, 255, 'not null', '动作URL')
                ->addColumn(self::fields_MODULE, TableInterface::column_type_VARCHAR, 255, 'not null', '模块')
                ->create();
        } else {
            $setup->getPrinting()->warning('数据表存在，跳过安装数据表...' . self::table);
        }

    }

    function provideTable(): string
    {
        return self::table;
    }

    public function getName(): string
    {
        return parent::getData(self::fields_NAME);
    }

    public function setName(string $name): static
    {
        return parent::setData(self::fields_NAME, $name);
    }

    public function getPid(): string
    {
        return parent::getData(self::fields_PID);
    }

    public function setPid(string $pid): static
    {
        return parent::setData(self::fields_NAME, $pid);
    }

    public function getSource(): string
    {
        return parent::getData(self::fields_SOURCE);
    }

    public function setSource(string $source): static
    {
        return parent::setData(self::fields_SOURCE, $source);
    }

    public function getParentSource(): string
    {
        return parent::getData(self::fields_PARENT_SOURCE);
    }

    public function setParentSource(string $source): static
    {
        return parent::setData(self::fields_PARENT_SOURCE, $source);
    }

    public function getAction(): string
    {
        return parent::getData(self::fields_ACTION);
    }

    public function setAction(string $url): static
    {
        return parent::setData(self::fields_ACTION, $url);
    }

    public function getModule(): string
    {
        return $this->getData(self::fields_MODULE);
    }

    public function setModule(string $module_name): static
    {
        return $this->setData(self::fields_MODULE, $module_name);
    }

    /*----------------------助手函数区-------------------------*/
    function getUrl(): string
    {
        return $this->url->build($this->getAction());
    }

    /**
     * @DESC          # 获取菜单树
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/9 0:38
     * 参数区：
     */
    function getMenuTree(): mixed
    {
        $cache_key = 'backend_menu_cache';
        if($data = $this->backendCache->get( $cache_key )){
            p($data );
            return $data;
        }
        $top_menus = $this->where($this::fields_PID.' is null')->select()->fetch();
        foreach ($top_menus as &$top_menu) {
            $top_menu = $this->getSubMenu($top_menu);
        }
        $this->backendCache->set($cache_key, $top_menus);
        return $top_menus;
    }

    function getSubMenus(\Weline\Backend\Model\Menu $menu){
        if($sub_menus = $this->clearData()->where($this::fields_PID,$menu->getId())->select()->fetch()){
            foreach ($sub_menus as $sub_menu) {
                $has_sub_menu = $this->clearData()->where($this::fields_PID,$sub_menu->getID())->find()->fetch();
                if($has_sub_menu->getId()){
                    return $this->getSubMenus($sub_menu);
                }
            }
            return $menu->setSubMenu($sub_menus);
        }else{
            return $menu->setSubMenu([]);
        }
    }
}