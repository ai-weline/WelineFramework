<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Controller;

class Index extends BasePcController
{
    public function index()
    {
        $this->assign('data', 122);

        return $this->fetch();
    }

    public function test()
    {
        $this->assign('data', 123);
        $this->assign('title', 123);

        return $this->fetch();
    }
}
