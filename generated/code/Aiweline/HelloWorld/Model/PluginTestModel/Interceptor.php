<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Model\PluginTestModel;

class Interceptor extends \Aiweline\HelloWorld\Model\PluginTestModel
{
    // 继承侦听器trait
    use \Weline\Framework\Interception\Interceptor;

    public function getName(
        $a
    ) {
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, 'getName');
        if (! $pluginInfo) {
            return parent::getName($a);
        }

        return $this->___callPlugins('getName', func_get_args(), $pluginInfo);
    }

    public function __construct()
    {
        $this->___init();
    }
}
