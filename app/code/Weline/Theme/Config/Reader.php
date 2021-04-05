<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Config;

use Weline\Framework\Cache\CacheInterface;
use Weline\Theme\Cache\ThemeCache;
use Weline\Theme\Model\WelineTheme;

class Reader
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $themeCache;

    /**
     * @var WelineTheme
     */
    private WelineTheme $theme;

    /**
     * Reader 初始函数...
     * @param ThemeCache $themeCache
     * @param WelineTheme $theme
     */
    public function __construct(
        ThemeCache $themeCache,
        WelineTheme $theme
    ) {
        $this->themeCache = $themeCache->create();
        $this->theme      = $theme;
    }

    public function getTheme(bool $cache = true)
    {
        if ($cache) {
            return $this->theme->getActiveTheme();
        }

        return $this->theme->load(WelineTheme::filed_IS_ACTIVE);
    }
}
