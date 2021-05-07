<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\Controller;

use Weline\Framework\App\Controller\FrontendController;
use Weline\Framework\Event\EventsManager;

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
     * @param int $a
     * @throws \Weline\Framework\App\Exception
     * @return bool
     */
    public function index()
    {
        return $this->fetch();
    }
}
