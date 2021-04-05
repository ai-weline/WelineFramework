<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Model;

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
    )
    {
        parent::__construct($data);
    }

    function __init()
    {
        $this->themeCache = ObjectManager::getInstance(ThemeCache::class)->create();
    }

    /**
     * @DESC         |获取激活的主题 有缓存
     *
     * 参数区：
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function getActiveTheme()
    {
        if ($theme = $this->themeCache->get('theme')) {
            return $theme;
        }
        $theme = $this->load(self::filed_IS_ACTIVE, 1);
        $this->themeCache->set('theme', $theme, static::cache_TIME);
        return $theme;
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
        return $this->getData(self::filed_PATH);
    }

    public function setPath($value)
    {
        $this->setData(self::filed_PATH, $value);
        return $this;
    }

    function getParentId()
    {
        return $this->getData(self::filed_PARENT_ID);
    }

    function setParentId($value)
    {
        $this->setData(self::filed_PARENT_ID, $value);
        return $this;
    }

    function isActive()
    {
        return $this->getData(self::filed_IS_ACTIVE);
    }

    function setIsActive($value)
    {
        $this->setData(self::filed_IS_ACTIVE, $value);
        return $this;
    }

    function getCreateTime()
    {
        return $this->getData(self::filed_CREATE_TIME);
    }

    function setCreateTime($time)
    {
        $this->setData(self::filed_CREATE_TIME, $time);
        return $this;
    }
}
