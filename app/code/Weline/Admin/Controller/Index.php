<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller;

use Weline\Framework\App\Controller\BackendController;
use Weline\Framework\App\Session\BackendSession;
use Weline\Framework\Session\Session;

class Index extends BackendController
{
    private BackendSession $backendSession;

    function __construct(
        BackendSession $backendSession
    )
    {
        $this->backendSession = $backendSession;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     */
    public function index()
    {
        if ($this->backendSession->isLogin()) {
            return $this->fetch();
        }
        return $this->fetch('login');
    }

    public function test(): string
    {
        return '111111111';
    }
}
