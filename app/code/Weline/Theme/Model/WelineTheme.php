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
use Weline\Framework\Database\Model;
use Weline\Framework\Manager\ObjectManager;
use Weline\Theme\Cache\ThemeCache;
use Weline\Theme\Setup\Install;

class WelineTheme extends Model
{
    const cache_TIME = 604800;

    const filed_ID = 'id';

    const filed_NAME = 'name';

    const filed_PATH = 'path';

    const filed_PARENT_ID = 'parent_id';

    const filed_IS_ACTIVE = 'is_active';

    const filed_CREATE_TIME = 'create_time';

    protected $pk = self::filed_ID;

//    protected $table = Install::table_THEME; # 如果需要设置特殊表名 需要加前缀

    /**
     * @var CacheInterface
     */
    private CacheInterface $themeCache;

    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }

    public function __init()
    {
        $this->themeCache = ObjectManager::getInstance(ThemeCache::class)->create();
    }

    /**
     * @DESC         |获取激活的主题 有缓存
     *
     * 参数区：
     *
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     * @return mixed
     */
    public function getActiveTheme()
    {
        if ($theme = $this->themeCache->get('theme')) {
            return $theme;
        }
        $this->load(self::filed_IS_ACTIVE, 1);
        if ($this->getId()) {
            $this->themeCache->set('theme', $this, static::cache_TIME);
            Env::getInstance()->setConfig('theme', $this->getData());
        }

        return $this;
    }

    public function getId()
    {
        return $this->getData(self::filed_ID);
    }

    public function setId($value)
    {
        $this->setData(self::filed_ID, $value);

        return $this;
    }

    public function getName_()
    {
        return $this->getData(self::filed_NAME);
    }

    public function setName($value)
    {
        $this->setData(self::filed_NAME, $value);

        return $this;
    }

    public function getPath()
    {
        return Env::path_THEME_DESIGN_DIR . str_replace('\\', DIRECTORY_SEPARATOR, $this->getData(self::filed_PATH)) . DIRECTORY_SEPARATOR;
    }

    public function getRelatePath()
    {
        return str_replace(BP,'',Env::path_THEME_DESIGN_DIR) . str_replace('\\', DIRECTORY_SEPARATOR, $this->getData(self::filed_PATH)) . DIRECTORY_SEPARATOR;
    }

    public function setPath($value)
    {
        $this->setData(self::filed_PATH, $value);

        return $this;
    }

    public function getParentId()
    {
        return $this->getData(self::filed_PARENT_ID);
    }

    public function setParentId($value)
    {
        $this->setData(self::filed_PARENT_ID, $value);

        return $this;
    }

    public function isActive()
    {
        return $this->getData(self::filed_IS_ACTIVE);
    }

    public function setIsActive(bool $value)
    {
        $this->setData(self::filed_IS_ACTIVE, $value);

        return $this;
    }

    public function getCreateTime()
    {
        return $this->getData(self::filed_CREATE_TIME);
    }

    public function setCreateTime($time)
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
        if ($this->isActive()) {
            $this->where(self::filed_NAME, $this->getName_())
                ->find();
            if ($this->getId()) {
                $this->getDb()
                    ->query('UPDATE ' . $this->getTable() . ' SET `is_active`=0 WHERE id != ' . $this->getId());
            }
        }
    }
}
