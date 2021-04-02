<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Model;

use Weline\Framework\Cache\CacheInterface;
use Weline\Theme\Cache\ThemeCache;
use Weline\Theme\Model\ResourceModel\WelineTheme;

class Theme
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
        WelineTheme $welineTheme
    ) {
        $this->themeCache  = $themeCache->create();
        $this->welineTheme = $welineTheme;
    }

    protected function getModel()
    {
        if ($theme = $this->themeCache->get('theme')) {
            return $theme;
        }
        p($this->welineTheme->where());
        p($this->welineTheme->getData('id'));
        $this->themeCache->set('theme', $this->welineTheme, static::cache_TIME);
        p($this->themeCache->get('theme'));

        return $this->themeCache->get('theme');
    }

    public function getId()
    {
        p($this->getModel()->getData());
    }
}
