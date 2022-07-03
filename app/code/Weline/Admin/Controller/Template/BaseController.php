<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\Template;

class BaseController extends \Weline\Admin\Controller\BaseController
{
    public function __init()
    {
        # 设置模板文件为html
        parent::__init();
        $this->getTemplate()->setFileExt('html');
    }
}
