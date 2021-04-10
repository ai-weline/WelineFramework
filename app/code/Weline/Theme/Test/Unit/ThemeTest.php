<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Test\Unit;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;
use Weline\Theme\Model\WelineTheme;

class ThemeTest extends TestCore
{
    /**
     * @var WelineTheme
     */
    private WelineTheme $theme;

    public function testGetMode()
    {
        $this->theme = ObjectManager::getInstance(WelineTheme::class);
        $theme       = $this->theme->load(1);
        p($theme->getActiveTheme());
    }
}
