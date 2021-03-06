<?php
/**
 * 文件信息 Weline框架自动侦听拦截类，请勿随意修改，以免造成系统异常
 * 作者：WelineFramework                       【Aiweline/邹万才】
 * 网名：WelineFramework框架                    【秋风雁飞(Aiweline)】
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：WelineFramework框架
 * 日期：2021-03-06
 * 时间：23:03:44
 * 描述：此文件源码由WelineFramework框架自动侦听拦截类，请勿随意修改源码，以免造成系统异常！
 */
namespace Aiweline\HelloWorld\Model\PluginTestModel;

class Interceptor extends \Aiweline\HelloWorld\Model\PluginTestModel
{
    // 继承侦听器trait
    use \Weline\Framework\Interception\Interceptor;
    
    
    public function getName(
         $a
    )
    {
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, 'getName');
        if (!$pluginInfo) {
            return parent::getName($a);
        } else {
            return $this->___callPlugins('getName', func_get_args(), $pluginInfo);
        } 
    }

    public function __construct()
    {
        $this->___init();
    }
}
