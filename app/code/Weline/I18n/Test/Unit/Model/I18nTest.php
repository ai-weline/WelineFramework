<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Test\Unit\Model;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;
use Weline\I18n\Model\I18n;

class I18nTest extends TestCore
{
    public function testGetLocals()
    {
        /**@var I18n $i18n*/
        $i18n = ObjectManager::getInstance(I18n::class);
        p($i18n->getLocals());
    }
}
