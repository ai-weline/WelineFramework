<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
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

    public function index()
    {
        // 分配事件
        $a = 1;
        $this->eventsManager->dispatch('Aiweline_Index::index_event_test', ['a'=>$a]);
        p($a);

        return $this->fetch();
    }
}
