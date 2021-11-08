<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App\Controller;

use Weline\Framework\App\Session\BackendApiSession;
use Weline\Framework\App\Session\BackendSession;
use Weline\Framework\Controller\AbstractRestController;
use Weline\Framework\Manager\ObjectManager;

class BackendRestController extends AbstractRestController
{

    function __init()
    {
        parent::__init();
        $this->getSession(BackendApiSession::class);
    }

    private ?BackendApiSession $session = null;
}
