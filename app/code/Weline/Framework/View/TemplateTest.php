<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\View;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class TemplateTest extends TestCore
{
    public function testGetFile()
    {
        /**@var Template $template*/
        $template = ObjectManager::getInstance(Template::class);
        p($template->getFile('1.txt'));
    }
}
