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
    /**
     * @DESC         |修改系统读取文件的路径
     *
     * 参数区：
     *
     * @param \Weline\Framework\View\Template $subject
     * @param \Closure $call
     * @param $file
     * @return mixed
     */
    public function aroundGetFile(\Weline\Framework\View\Template $subject, \Closure $call, $file)
    {
        $file = '2.txt';

        return $call($file);
    }
}
