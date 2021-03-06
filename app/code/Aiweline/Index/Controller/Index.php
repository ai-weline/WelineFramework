<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\Controller;

use Weline\Framework\App\Controller\FrontendController;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;

class Index extends FrontendController
{
    /**
     * @var EventsManager
     */
    private EventsManager $eventsManager;

    public function __construct(
        EventsManager $eventsManager
    )
    {
        $this->eventsManager = $eventsManager;
        $this->pluginTestModel = $pluginTestModel;
    }

    /**
     * @DESC         |首页
     *
     * 参数区：
     *
     * @param int $a
     * @return bool
     * @throws \Weline\Framework\App\Exception
     */
    public function index()
    {
        return $this->fetch();
    }
}
