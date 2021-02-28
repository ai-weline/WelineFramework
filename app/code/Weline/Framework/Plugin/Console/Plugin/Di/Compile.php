<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin\Console\Plugin\Di;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Plugin\PluginsManager;
use Weline\Framework\Plugin\Proxy\Generator;

class Compile implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var PluginsManager
     */
    private PluginsManager $pluginsManager;

    public function __construct(
        PluginsManager $pluginsManager
    ) {
        $this->pluginsManager = $pluginsManager;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $plugins_list = $this->pluginsManager->scanPlugins();
        foreach ($plugins_list as $class => $plugins) {
            Generator::getProxy(ObjectManager::getInstance($class), $plugins);
        }
        p($plugins_list);
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '系统依赖编译';
    }
}
