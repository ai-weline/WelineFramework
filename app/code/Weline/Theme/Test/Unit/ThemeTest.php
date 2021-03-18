<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/3/18
 * 时间：22:00
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
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


    function testGetMode(){
        $this->theme = ObjectManager::getInstance(Theme::class);
        $this->theme->getId();
    }
}
