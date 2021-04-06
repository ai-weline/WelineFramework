<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Plugin;

class Template
{
    public function aroundGetFile(\Weline\Framework\View\Template $subject, \Closure $call, $file)
    {
        $result = $call();
        p($result);
    }
}
