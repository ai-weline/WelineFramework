<?php
/**
 * 文件信息 Weline框架自动侦听拦截类，请勿随意修改，以免造成系统异常
 * 作者：WelineFramework                       【Aiweline/邹万才】
 * 网名：WelineFramework框架                    【秋风雁飞(Aiweline)】
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：WelineFramework框架
 * 日期：2021-03-06
 * 时间：21:03:16
 * 描述：此文件源码由WelineFramework框架自动侦听拦截类，请勿随意修改源码，以免造成系统异常！
 */
namespace Aiweline\Index\Controller\Index;

class Interceptor extends \Aiweline\Index\Controller\Index
{
    // 继承侦听器trait
    use \Weline\Framework\Interception\Interceptor;
    
    
    public function __construct(
        \Weline\Framework\Event\EventsManager $eventsManager,
        \Aiweline\Index\Model\PluginTestModel $pluginTestModel
    )
    {
        
        $this->___init();
        parent::__construct($eventsManager,
        $pluginTestModel);
                    
    }
    
    
    public function plugin(
        string $a='1'
    )
    {
        
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, 'plugin');
        if (!$pluginInfo) {
            return parent::plugin($a);
        } else {
            return $this->___callPlugins('plugin', func_get_args(), $pluginInfo);
        } 
    }
}
