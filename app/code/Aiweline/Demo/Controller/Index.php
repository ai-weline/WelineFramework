<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Demo\Controller;

use Weline\Framework\App\Controller\FrontendController;
use Weline\Framework\App\Env;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;
use Weline\Theme\Model\WelineTheme;

class Index extends FrontendController
{
    /**
     * @var EventsManager
     */
    private EventsManager $eventsManager;

    public function __construct(
        EventsManager $eventsManager
    ) {
        $this->eventsManager = $eventsManager;
    }

    /**
     * @DESC         |首页
     *
     * 参数区：
     *
     */
    public function index()
    {
        /**
         * @var WelineTheme $theme
         */
        $theme = ObjectManager::getInstance(WelineTheme::class);
        $this->assign('theme', $theme->getActiveTheme());
        return $this->fetch();
    }

    /**
     * @DESC          # 测试调用其他模块的模板文件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/12 12:19
     * 参数区：
     * @return bool|void
     */
    public function moduleTemplateFetch()
    {
        return $this->fetch('Aiweline_HelloWorld::templates/HelloWorld/demo.phtml');
    }

    /**
     * @DESC          # 测试调用其他模块的模板文件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/12 12:19
     * 参数区：
     * @return bool|void
     */
    public function getComposerVendorModuleTemplate()
    {
        return $this->fetch('Aiweline_WebsiteMonitoring::templates/Index/index.phtml');
    }
}
