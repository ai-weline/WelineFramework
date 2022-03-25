<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\System;

use Weline\Framework\Event\Config\Reader;
use Weline\Framework\Manager\ObjectManager;

class Events extends \Weline\Admin\Controller\BaseController
{
    public ?Reader $reader;

    public function getIndex()
    {
        $events = $this->getReader()->read();
        $this->assign('events', $events);
        return $this->fetch();
    }

    private function getReader(): Reader
    {
        if (empty($this->reader)) {
            $this->reader = ObjectManager::getInstance(Reader::class);
        }
        return $this->reader;
    }
}
