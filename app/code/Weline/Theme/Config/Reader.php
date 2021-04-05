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

class Reader
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $themeCache;

    /**
     * Reader 初始函数...
     * @param ThemeCache $themeCache
     */
    public function __construct(
        ThemeCache $themeCache
    ) {
        $this->themeCache = $themeCache->create();
    }

    public function getTheme(bool $cache = true)
    {
        if ($cache && $theme = $this->themeCache->get('theme')) {
            return $theme;
        }

    }
}
