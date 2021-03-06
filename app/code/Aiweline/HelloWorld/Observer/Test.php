<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Observer;

use Weline\Framework\Event\Event;
use Weline\Framework\Event\ObserverInterface;

class Test implements ObserverInterface
{
    public function execute(Event $event)
    {
        $a = $event->getData('a');
        p('我是观察者', 1);
        $a->setData('a', 2);
    }
}
