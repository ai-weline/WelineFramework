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
use Weline\Theme\Cache\ThemeCache;

class WelineTheme extends Model
{
    const cache_TIME = 604800;

    /**
     * @var CacheInterface
     */
    private CacheInterface $themeCache;

    /**
     * @var WelineTheme
     */
    private WelineTheme $welineTheme;

    public function __construct(
        ThemeCache $themeCache,
        array $data = []
    )
    {
        $this->themeCache = $themeCache->create();
        parent::__construct($data);
    }

    protected function getModel()
    {
        if ($theme = $this->themeCache->get('theme')) {
            p($theme);
            return $theme;
        }
        $theme = $this->load('is_active', 1);
        p($theme);
        $this->themeCache->set('theme', $this->welineTheme, static::cache_TIME);
        p($this->themeCache->get('theme'));

        return $this->themeCache->get('theme');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function setId($value)
    {
        $this->setData('id', $value);
        return $this;
    }

    public function getName_()
    {
        return $this->getData('name');
    }

    public function setName($value)
    {
        $this->setData('name', $value);
        return $this;
    }

    function getParentId()
    {
        return $this->getData('parent_id');
    }

    function setParentId($value)
    {
        $this->setData('parent_id', $value);
        return $this;
    }

    function isActive()
    {
        return $this->getData('is_active');
    }

    function setIsActive($value)
    {
        $this->setData('is_active', $value);
        return $this;
    }

    function getCreateTime()
    {
        return $this->getData('create_time');
    }

    function setCreateTime($time)
    {
        $this->setData('create_time', $time);
        return $this;
    }
}
