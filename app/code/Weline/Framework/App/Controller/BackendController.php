<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App\Controller;

use Weline\Framework\App\Session\BackendSession;
use Weline\Framework\Controller\PcController;
use Weline\Framework\Manager\ObjectManager;

class BackendController extends PcController
{
    private ?BackendSession $session = null;

    function __init()
    {
        parent::__init();
        $this->getSession();
    }

    function getSession()
    {
        if (!$this->session) {
            $this->session = ObjectManager::getInstance(BackendSession::class);
        }
        return $this->session;
    }
}
