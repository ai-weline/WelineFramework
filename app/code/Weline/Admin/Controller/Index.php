<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller;

use Weline\Framework\App\Controller\BackendController;

class Index extends BaseController
{
    public function index()
    {
        $this->fetch();
    }

    public function test(): string
    {
        return '111111111';
    }
}
