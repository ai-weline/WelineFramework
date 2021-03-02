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
    ) {
        $this->eventsManager = $eventsManager;
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
        $e = 1,
        $test = 'wozao',
        $arr = ['a'=>1, 2, 3]
    ) {
        $re =  $this->fetch();
        p();

        return $re;
    }

    public function observer()
    {
        // 分配事件
        $a = new DataObject(['a' => 1]);
        p($a->getData('a'), 1);
        $this->eventsManager->dispatch('Aiweline_Index::test_observer', ['a' => $a]);
        p($a->getData('a'));

        return $this->fetch();
    }
}
