<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller;

use Weline\Framework\App\Controller\BackendController;

class Index extends BackendController
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @throws \Weline\Framework\App\Exception
     * @return bool
     */
    public function index()
    {
        return $this->fetch();
    }

    public function test()
    {
        return '111111111';
    }
}
