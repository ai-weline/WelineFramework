<?php
/**
 * 文件信息 Weline框架自动侦听拦截类，请勿随意修改，以免造成系统异常
 * 作者：WelineFramework                       【Aiweline/邹万才】
 * 网名：WelineFramework框架                    【秋风雁飞(Aiweline)】
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：WelineFramework框架
 * 日期：2021-03-03
 * 时间：01:03:58
 * 描述：此文件源码由WelineFramework框架自动侦听拦截类，请勿随意修改源码，以免造成系统异常！
 */
namespace Aiweline\Index\Controller\Index;

class Interceptor extends \Aiweline\Index\Controller\Index
{
    // 继承侦听器trait
    use \Weline\Framework\Interception\Interceptor;
    
    
    public function __construct(
        \Weline\Framework\Event\EventsManager $eventsManager
    )
    {
        
        $this->___init();
        parent::__construct($eventsManager);
                    
    }
    
    /**
     * @DESC         |首页
     *
     * 参数区：
     *
     * @param mixed $e
     * @param mixed $test
     * @param mixed $arr
     * @throws \Weline\Framework\App\Exception
     * @return bool
     */
    public function index(
         $e=1,
         $test='wozao',
         $arr=array (
  'a' => 1,
  0 => 2,
  1 => 3,
)
    )
    {
        
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, 'index');
        if (!$pluginInfo) {
            return parent::index($e,
        $test,
        $arr);
        } else {
            return $this->___callPlugins('index', func_get_args(), $pluginInfo);
        } 
    }
}
