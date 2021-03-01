<?php
/**
 * 文件信息 Weline框架自动侦听拦截类，请勿随意修改，以免造成系统异常
 * 作者：WelineFramework                       【Aiweline/邹万才】
 * 网名：WelineFramework框架                    【秋风雁飞(Aiweline)】
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：WelineFramework框架
 * 日期：2021-03-01
 * 时间：01:03:00
 * 描述：此文件源码由WelineFramework框架自动侦听拦截类，请勿随意修改源码，以免造成系统异常！
 */
namespace Aiweline\Index\Controller\Test;

class Interceptor extends \Aiweline\Index\Controller\Test
{
    // 继承侦听器trait
    use \Weline\Framework\Interception\Interceptor;
    
    /**
     * Test 初始函数...
     * @param State $state
     */
    public function __construct(
        \Weline\Framework\App\State $state
    )
    {
        
        $this->___init();
        parent::__construct($state);
                    
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, '__construct');
        if (!$pluginInfo) {
            return parent::__construct($state);
        } else {
            return $this->___callPlugins('__construct', func_get_args(), $pluginInfo);
        } 
    }
    
    
    public function Dd(
        
    )
    {
        
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, 'Dd');
        if (!$pluginInfo) {
            return parent::Dd();
        } else {
            return $this->___callPlugins('Dd', func_get_args(), $pluginInfo);
        } 
    }
}
