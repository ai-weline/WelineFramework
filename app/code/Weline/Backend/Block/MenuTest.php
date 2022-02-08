<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Block;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class MenuTest extends TestCore
{
    private Menu $menu;
    function setUp(): void
    {
        $this->menu = ObjectManager::getInstance(Menu::class);
    }

    function testGetMenu(){
        p($this->menu->getMenus());
    }
    function testGetMenuTree(){
        p($this->menu->getMenuTree());
    }
}
