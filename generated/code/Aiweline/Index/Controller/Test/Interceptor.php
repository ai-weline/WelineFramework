<?php
/**
 * 文件信息 Weline框架自动侦听拦截类，请勿随意修改，以免造成系统异常
 * 作者：WelineFramework                       【Aiweline/邹万才】
 * 网名：WelineFramework框架                    【秋风雁飞(Aiweline)】
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：WelineFramework框架
 * 日期：2021-02-28
 * 时间：17:02:43
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
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, '__construct');
        if (!$pluginInfo) {
            return parent::__construct($state);
        } else {
            return $this->___callPlugins('__construct', func_get_args(), $pluginInfo);
        } 
    }
    
    
    public function index(
        
    )
    {
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, 'index');
        if (!$pluginInfo) {
            return parent::index();
        } else {
            return $this->___callPlugins('index', func_get_args(), $pluginInfo);
        } 
    }
}
