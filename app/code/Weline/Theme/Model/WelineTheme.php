<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Model;

use Weline\Framework\App\Env;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Database\Model;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\Setup\Db\Setup;
use Weline\Theme\Cache\ThemeCache;

class WelineTheme extends Model
{
    const cache_TIME = 604800;

    const filed_ID = 'id';

    const filed_NAME = 'name';

    const filed_PATH = 'path';

    const filed_PARENT_ID = 'parent_id';

    const filed_IS_ACTIVE = 'is_active';

    const filed_CREATE_TIME = 'create_time';

    protected string $pk = self::filed_ID;

//    protected $table = Install::table_THEME; # 如果需要设置特殊表名 需要加前缀

    /**
     * @var CacheInterface
     */
    private CacheInterface $themeCache;

     public function __construct(
        array $data = []
    )
    {
        parent::__construct($data);
    }

    public function __init()
    {
        $this->themeCache = ObjectManager::getInstance(ThemeCache::class)->create();
        parent::__init();
    }

    /**
     * @DESC          # 获取激活的主题 有缓存
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 21:15
     * 参数区：
     * @return $this
     * @throws \ReflectionException
     * @throws \Weline\Framework\Exception\Core
     */
    public function getActiveTheme(): static
    {
        if ($theme = $this->themeCache->get('theme')) {
            return $this->setData($theme);
        }
        $this->load(self::filed_IS_ACTIVE, 1);

        if ($this->getId()) {
            $this->themeCache->set('theme', $this->getData(), static::cache_TIME);
            Env::getInstance()->setConfig('theme', $this->getData());
        }
        return $this;
    }

    public function getName()
    {
        return $this->getData(self::filed_NAME);
    }

    public function setName($value): static
    {
        $this->setData(self::filed_NAME, $value);

        return $this;
    }

    public function getPath(): string
    {
        return Env::path_THEME_DESIGN_DIR . str_replace('\\', DIRECTORY_SEPARATOR, $this->getData(self::filed_PATH)) . DIRECTORY_SEPARATOR;
    }

    public function getRelatePath(): string
    {
        return str_replace(BP, '', Env::path_THEME_DESIGN_DIR) . str_replace('\\', DIRECTORY_SEPARATOR, $this->getData(self::filed_PATH)) . DIRECTORY_SEPARATOR;
    }

    public function setPath($value): static
    {
        $this->setData(self::filed_PATH, $value);

        return $this;
    }

    public function getParentId()
    {
        return $this->getData(self::filed_PARENT_ID);
    }

    public function setParentId($value): static
    {
        $this->setData(self::filed_PARENT_ID, $value);

        return $this;
    }

    public function isActive()
    {
        return $this->getData(self::filed_IS_ACTIVE);
    }

    public function setIsActive(bool $value): static
    {
        $this->setData(self::filed_IS_ACTIVE, $value);

        return $this;
    }

    public function getCreateTime()
    {
        return $this->getData(self::filed_CREATE_TIME);
    }

    public function setCreateTime($time): static
    {
        $this->setData(self::filed_CREATE_TIME, $time);

        return $this;
    }

    /**
     * @DESC         |保存之后如果当前主题处于激活状态则启用当前主题
     * 启用前清除所有缓存
     * 启用当前主题则将其他主题设置为不激活
     *
     * 参数区：
     */
    public function save_after()
    {
        if ($this->isActive() && $this->getId()) {
            #$this->query('UPDATE ' . $this->getTable() . ' SET `is_active`=0 WHERE id != ' . $this->getId())->fetch();
            $this->getQuery()
                ->where(self::filed_IS_ACTIVE, 1)
                ->where(self::filed_ID, $this->getId(), '!=')
                ->update(self::filed_IS_ACTIVE,0)
                ->fetch();
        }
    }

    function provideTable(): string
    {
        return '';
    }

    function setup(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement setup() method.
    }

    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    function install(ModelSetup $setup, Context $context): void
    {
        if(!$setup->tableExist()){
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
