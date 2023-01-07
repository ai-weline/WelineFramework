<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/7 22:13:53
 */

namespace Weline\Acl\Observer;

use Weline\Framework\Event\Event;

class ControllerClassAttribute implements \Weline\Framework\Event\ObserverInterface
{

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $type = $event->getData('type');
        $data = $event->getData('data');
//        p($data);
    }
}