<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Cache;

class ThemeCache extends \Weline\Framework\Cache\CacheManager
{
    public function __construct(string $identity = 'theme_system')
    {
        parent::__construct($identity);
    }
}
