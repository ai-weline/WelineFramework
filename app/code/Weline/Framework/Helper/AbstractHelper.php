<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Helper;

use Weline\Framework\Output\Debug\Printing;

class AbstractHelper
{
    protected Printing $_debug;

    public function __construct()
    {
        $this->_debug = new Printing();
    }
}
