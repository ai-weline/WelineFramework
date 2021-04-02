<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Test\Unit;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;
use Weline\Theme\Model\Theme;

class ThemeTest extends TestCore
{
    /**
     * @var Theme
     */
    private Theme $theme;

    public function testGetMode()
    {
        $this->theme = ObjectManager::getInstance(Theme::class);
        $this->theme->getId();
    }
}
