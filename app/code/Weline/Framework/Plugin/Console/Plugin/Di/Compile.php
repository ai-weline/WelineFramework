<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/2/17
 * 时间：20:18
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
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


    function __construct(
        PluginsManager $pluginsManager
    )
    {
        $this->pluginsManager = $pluginsManager;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $plugins_list = $this->pluginsManager->scanPlugins();
        foreach ($plugins_list as $class => $plugins) {
            Generator::getProxy(ObjectManager::getInstance($class),$plugins);
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