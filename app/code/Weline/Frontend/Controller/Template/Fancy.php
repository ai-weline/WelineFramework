<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Controller\Template;

use Weline\Framework\App\Controller\FrontendController;

class Fancy extends FrontendController
{
    public function __construct()
    {
        $this->getTemplate()->setFileExt('html');
    }

    public function index()
    {
        return $this->fetch();
    }
}
