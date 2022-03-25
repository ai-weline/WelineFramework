<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\test;

use Weline\Backend\Model\Config;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\UnitTest\TestCore;

class ConfigTest extends TestCore
{
    protected Config $config;

    public function setUp(): void
    {
        $this->config = ObjectManager::getInstance(Config::class);
    }

    public function testGetConfig()
    {
//        p($this->config->setConfig('header', '000','Weline_Backend'));
//        p($this->config->getConfig('header','Weline_Backend'));
    }
}
