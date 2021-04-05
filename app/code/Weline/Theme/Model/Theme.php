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
use Weline\Theme\Model\ResourceModel\WelineTheme;

class Theme extends Model
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
        WelineTheme $welineTheme,
        array $data = []
    )
    {
        $this->themeCache = $themeCache->create();
        $this->welineTheme = $welineTheme;
        parent::__construct($data);
    }

    protected function getModel()
    {
        if ($theme = $this->themeCache->get('theme')) {
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
        return $this->setData('id', $value);
    }
}
