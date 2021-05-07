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
    private I18n $i18n;

    public function setUp(): void
    {
        $this->i18n = ObjectManager::getInstance(I18n::class);
    }

    public function testGetLocals()
    {
        p($this->i18n->getLocals());
    }

    public function testGetLocalWords()
    {
        p($this->i18n->getLocalsWords());
    }
}
